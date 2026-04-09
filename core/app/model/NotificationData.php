<?php
class NotificationData {
    public static $tablename = "notification_log";

    public function __construct(){
        $this->notification_type_id = "";
        $this->recipient_email = "";
        $this->recipient_name = "";
        $this->recipient_type = "";
        $this->subject = "";
        $this->body = "";
        $this->status = "pending";
        $this->reference_id = "";
        $this->reference_type = "";
        $this->created_at = "NOW()";
    }

    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (notification_type_id, recipient_email, recipient_name, recipient_type, subject, body, status, reference_id, reference_type, created_at) ";
        $sql .= "VALUES ($this->notification_type_id, \"$this->recipient_email\", \"$this->recipient_name\", \"$this->recipient_type\", \"$this->subject\", \"$this->body\", \"$this->status\", $this->reference_id, \"$this->reference_type\", $this->created_at)";
        return Executor::doit($sql);
    }

    public function update(){
        $sql = "UPDATE ".self::$tablename." SET status=\"$this->status\", error_message=\"$this->error_message\", sent_at=\"$this->sent_at\" WHERE id=$this->id";
        Executor::doit($sql);
    }    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new NotificationData());
        }
        return null;
    }

    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename." ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationData());
        }
        return array();
    }

    public static function getByStatus($status){
        $sql = "SELECT * FROM ".self::$tablename." WHERE status='$status' ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationData());
        }
        return array();
    }

    public static function getByReference($reference_type, $reference_id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE reference_type='$reference_type' AND reference_id=$reference_id ORDER BY created_at DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationData());
        }
        return array();
    }    public static function getRecentNotifications($limit = 50){
        $sql = "SELECT n.*, nt.name as type_name FROM ".self::$tablename." n 
                INNER JOIN notification_types nt ON n.notification_type_id = nt.id 
                ORDER BY n.created_at DESC LIMIT $limit";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationData());
        }
        return array();
    }

    public static function getNotificationStats(){
        $sql = "SELECT 
                    status,
                    COUNT(*) as total,
                    DATE(created_at) as date
                FROM ".self::$tablename." 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY status, DATE(created_at)
                ORDER BY date DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return $query[0];
        }
        return false;
    }
}

class NotificationTypeData {
    public static $tablename = "notification_types";

    public function __construct(){
        $this->code = "";
        $this->name = "";
        $this->description = "";
        $this->template_subject = "";
        $this->template_body = "";
        $this->is_active = 1;
        $this->send_to_patient = 1;
        $this->send_to_medic = 1;
        $this->created_at = "NOW()";
    }

    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (code, name, description, template_subject, template_body, is_active, send_to_patient, send_to_medic, created_at) ";
        $sql .= "VALUES (\"$this->code\", \"$this->name\", \"$this->description\", \"$this->template_subject\", \"$this->template_body\", $this->is_active, $this->send_to_patient, $this->send_to_medic, $this->created_at)";
        return Executor::doit($sql);
    }    public function update(){
        $sql = "UPDATE ".self::$tablename." SET name=\"$this->name\", description=\"$this->description\", template_subject=\"$this->template_subject\", template_body=\"$this->template_body\", is_active=$this->is_active, send_to_patient=$this->send_to_patient, send_to_medic=$this->send_to_medic WHERE id=$this->id";
        return Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new NotificationTypeData());
        }
        return null;
    }    public static function getByCode($code){
        $sql = "SELECT * FROM ".self::$tablename." WHERE code='$code' AND is_active=1";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new NotificationTypeData());
        }
        return null;
    }

    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename." ORDER BY name ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationTypeData());
        }
        return array();
    }

    public static function getActive(){
        $sql = "SELECT * FROM ".self::$tablename." WHERE is_active=1 ORDER BY name ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationTypeData());
        }
        return array();
    }
}

class NotificationConfigData {
    public static $tablename = "notification_config";

    public function __construct(){
        $this->smtp_host = "smtp.gmail.com";
        $this->smtp_port = 587;
        $this->smtp_security = "tls";
        $this->smtp_username = "";
        $this->smtp_password = "";
        $this->from_email = "";
        $this->from_name = "Sistema Oncológico";
        $this->notifications_enabled = 1;
        $this->auto_send_enabled = 1;
        $this->created_at = "NOW()";
    }

    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (smtp_host, smtp_port, smtp_security, smtp_username, smtp_password, from_email, from_name, notifications_enabled, auto_send_enabled, created_at) ";
        $sql .= "VALUES (\"$this->smtp_host\", $this->smtp_port, \"$this->smtp_security\", \"$this->smtp_username\", \"$this->smtp_password\", \"$this->from_email\", \"$this->from_name\", $this->notifications_enabled, $this->auto_send_enabled, $this->created_at)";
        return Executor::doit($sql);
    }    public function update(){
        $sql = "UPDATE ".self::$tablename." SET smtp_host=\"$this->smtp_host\", smtp_port=$this->smtp_port, smtp_security=\"$this->smtp_security\", smtp_username=\"$this->smtp_username\", smtp_password=\"$this->smtp_password\", from_email=\"$this->from_email\", from_name=\"$this->from_name\", notifications_enabled=$this->notifications_enabled, auto_send_enabled=$this->auto_send_enabled WHERE id=$this->id";
        return Executor::doit($sql);
    }public static function getConfig(){
        $sql = "SELECT * FROM ".self::$tablename." ORDER BY id DESC LIMIT 1";
        $query = Executor::doit($sql);
        if($query && isset($query[0])){
            return Model::one($query[0], new NotificationConfigData());
        }
        return null;
    }    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new NotificationConfigData());
        }
        return null;
    }
}

class NotificationQueueData {
    public static $tablename = "notification_queue";

    public function __construct(){
        $this->notification_type_id = "";
        $this->recipient_email = "";
        $this->recipient_name = "";
        $this->recipient_type = "";
        $this->subject = "";
        $this->body = "";
        $this->scheduled_at = "";
        $this->reference_id = "";
        $this->reference_type = "";
        $this->attempts = 0;
        $this->max_attempts = 3;
        $this->status = "pending";
        $this->created_at = "NOW()";
    }

    public function add(){
        $sql = "INSERT INTO ".self::$tablename." (notification_type_id, recipient_email, recipient_name, recipient_type, subject, body, scheduled_at, reference_id, reference_type, attempts, max_attempts, status, created_at) ";
        $sql .= "VALUES ($this->notification_type_id, \"$this->recipient_email\", \"$this->recipient_name\", \"$this->recipient_type\", \"$this->subject\", \"$this->body\", \"$this->scheduled_at\", $this->reference_id, \"$this->reference_type\", $this->attempts, $this->max_attempts, \"$this->status\", $this->created_at)";
        return Executor::doit($sql);
    }    public function update(){
        $sql = "UPDATE ".self::$tablename." SET status=\"$this->status\", attempts=$this->attempts WHERE id=$this->id";
        return Executor::doit($sql);
    }

    public static function getById($id){
        $sql = "SELECT * FROM ".self::$tablename." WHERE id=$id";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::one($query[0], new NotificationQueueData());
        }
        return null;
    }    public static function getPendingNotifications(){
        $sql = "SELECT * FROM ".self::$tablename." WHERE status='pending' AND scheduled_at <= NOW() AND attempts < max_attempts ORDER BY scheduled_at ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationQueueData());
        }
        return array();
    }

    public static function getAll(){
        $sql = "SELECT * FROM ".self::$tablename." ORDER BY scheduled_at DESC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationQueueData());
        }
        return array();
    }
    
    public static function getByStatus($status){
        $sql = "SELECT * FROM ".self::$tablename." WHERE status='$status' ORDER BY scheduled_at ASC";
        $query = Executor::doit($sql);
        if($query && $query[0]){
            return Model::many($query[0], new NotificationQueueData());
        }
        return array();
    }
}
?>
