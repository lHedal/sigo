<?php
class OncologySchedulingService {
    
    /**
     * Asigna automáticamente una cita de oncología considerando:
     * - Disponibilidad de sillones
     * - Disponibilidad de médicos de oncología
     * - Prioridad del paciente en lista de espera
     */
    public static function autoAssignAppointment($waitlist_id) {
        $waitlist_item = OncologyWaitlistData::getById($waitlist_id);
        if (!$waitlist_item || $waitlist_item->status != 'pending') {
            return false;
        }

        // Obtener médicos de oncología disponibles
        $oncology_category = self::getOncologyCategory();
        if (!$oncology_category) return false;

        $available_slots = self::findAvailableSlots(
            $waitlist_item->requested_date,
            $waitlist_item->duration_minutes,
            $oncology_category->id
        );

        if (empty($available_slots)) {
            // Si no hay slots el día solicitado, buscar en los próximos 7 días
            for ($i = 1; $i <= 7; $i++) {
                $next_date = date('Y-m-d', strtotime($waitlist_item->requested_date . ' +' . $i . ' days'));
                $available_slots = self::findAvailableSlots(
                    $next_date,
                    $waitlist_item->duration_minutes,
                    $oncology_category->id
                );
                if (!empty($available_slots)) break;
            }
        }

        if (empty($available_slots)) {
            return false; // No hay slots disponibles
        }

        // Tomar el primer slot disponible
        $slot = $available_slots[0];
          // Crear la reserva
        $reservation = new ReservationData();
        $reservation->no = self::generateReservationNumber();
        $reservation->title = "Tratamiento Oncológico - " . $waitlist_item->treatment_type;
        $reservation->note = $waitlist_item->notes;
        $reservation->date_at = $slot['date'];
        $reservation->time_at = $slot['time'];
        $reservation->pacient_id = $waitlist_item->pacient_id;
        $reservation->medic_id = $slot['medic_id'];
        $reservation->chair_id = $slot['chair_id'];
        $reservation->duration = $waitlist_item->duration_minutes;
        $reservation->user_id = $_SESSION['user_id'] ?? 1; // Usuario actual o sistema
        $reservation->status_id = 1; // Pendiente
        $reservation->payment_id = 1; // Pendiente
        $reservation->sick = "";
        $reservation->symtoms = "";
        $reservation->medicaments = "";
        $reservation->price = "0";
        $reservation->created_at = "NOW()";
        
        $reservation_id = $reservation->add();
        
        if ($reservation_id) {
            // Actualizar lista de espera
            $waitlist_item->assignReservation($reservation_id);
            return $reservation_id;
        }
        
        return false;
    }

    /**
     * Encuentra slots disponibles considerando médicos y sillones
     */
    private static function findAvailableSlots($date, $duration_minutes, $oncology_category_id) {
        $slots = [];
        
        // Obtener médicos de oncología
        $medics = MedicData::getAllByCategory($oncology_category_id);
        
        // Obtener sillones disponibles
        $chairs = OncologyChairData::getAll();
        
        // Horario de trabajo: 8:00 AM a 6:00 PM
        $start_hour = 8;
        $end_hour = 18;
        $slot_duration = $duration_minutes;
        
        for ($hour = $start_hour; $hour < $end_hour; $hour++) {
            for ($minute = 0; $minute < 60; $minute += 30) { // Slots cada 30 minutos
                $time = sprintf("%02d:%02d:00", $hour, $minute);
                $end_time = date('H:i:s', strtotime($time . ' +' . $slot_duration . ' minutes'));
                
                // Verificar que el slot no se extienda más allá del horario de trabajo
                if (strtotime($end_time) > strtotime("18:00:00")) continue;
                
                foreach ($medics as $medic) {
                    foreach ($chairs as $chair) {
                        if (self::isSlotAvailable($date, $time, $end_time, $medic->id, $chair->id)) {
                            $slots[] = [
                                'date' => $date,
                                'time' => $time,
                                'end_time' => $end_time,
                                'medic_id' => $medic->id,
                                'chair_id' => $chair->id,
                                'medic_name' => $medic->name . ' ' . $medic->lastname
                            ];
                        }
                    }
                }
            }
        }
        
        return $slots;
    }

    /**
     * Verifica si un slot específico está disponible
     */
    private static function isSlotAvailable($date, $start_time, $end_time, $medic_id, $chair_id) {
        // Verificar conflictos con reservas existentes del médico
        $medic_conflicts = ReservationData::getByMedicAndDateTimeRange($medic_id, $date, $start_time, $end_time);
        if (!empty($medic_conflicts)) return false;
        
        // Verificar conflictos con reservas existentes del sillón
        $chair_conflicts = ReservationData::getByChairAndDateTimeRange($chair_id, $date, $start_time, $end_time);
        if (!empty($chair_conflicts)) return false;
        
        // Verificar horario del médico
        if (!self::isMedicAvailable($medic_id, $date, $start_time, $end_time)) return false;
        
        return true;
    }

    /**
     * Verifica si el médico está disponible en el horario solicitado
     */
    private static function isMedicAvailable($medic_id, $date, $start_time, $end_time) {
        $day_of_week = date('N', strtotime($date)); // 1=Monday, 7=Sunday
        $medic = MedicData::getById($medic_id);
        
        // Verificar horario del médico según el día de la semana
        $time_data_field = 'time' . $day_of_week . '_data';
        $schedule_data = $medic->$time_data_field;
        
        if (empty($schedule_data)) return false;
        
        // Asumir formato JSON: {"start":"08:00","end":"17:00"}
        $schedule = json_decode($schedule_data, true);
        if (!$schedule) return false;
        
        $medic_start = $schedule['start'] ?? '08:00';
        $medic_end = $schedule['end'] ?? '17:00';
        
        return (strtotime($start_time) >= strtotime($medic_start) && 
                strtotime($end_time) <= strtotime($medic_end));
    }

    /**
     * Obtiene la categoría de oncología
     */
    private static function getOncologyCategory() {
        $categories = CategoryData::getAll();
        foreach ($categories as $category) {
            if (stripos($category->name, 'oncolog') !== false) {
                return $category;
            }
        }
        return null;
    }

    /**
     * Genera un número de reserva único
     */
    private static function generateReservationNumber() {
        return "ONC-" . date('Ymd') . "-" . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }

    /**
     * Procesa automáticamente la lista de espera
     */
    public static function processWaitlist() {
        $pending_items = OncologyWaitlistData::getPending();
        $assigned_count = 0;
        
        foreach ($pending_items as $item) {
            if (self::autoAssignAppointment($item->id)) {
                $assigned_count++;
            }
        }
        
        return $assigned_count;
    }
}
?>
