<?php
/**
 * Bus Repository
 * Handles all bus-related queries
 */

require_once __DIR__ . '/../db.php';

class BusRepository {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function getAllBuses() {
        $sql = "SELECT * FROM bus_info";
        $result = $this->db->query($sql);
        $buses = [];
        
        while ($row = $this->db->fetchAssoc($result)) {
            $buses[] = $row;
        }
        
        return $buses;
    }

    public function getBusById($bus_id) {
        $bus_id = $this->db->escape($bus_id);
        $sql = "SELECT * FROM bus_info WHERE id = $bus_id";
        $result = $this->db->query($sql);
        return $this->db->fetchAssoc($result);
    }

    public function searchTrips($from, $to, $date = null) {
        $from = $this->db->escape($from);
        $to = $this->db->escape($to);
        
        $sql = "SELECT bl.*, bi.bus_name, bi.company, bi.image 
                FROM bus_lists bl
                JOIN bus_info bi ON bl.bus_id = bi.id
                WHERE bl.route_from = '$from' 
                AND bl.route_to = '$to'
                ORDER BY bl.departure_time ASC";
        
        $result = $this->db->query($sql);
        $trips = [];
        
        while ($row = $this->db->fetchAssoc($result)) {
            $trips[] = $row;
        }
        
        return $trips;
    }

    public function getTripDetails($trip_no) {
        $trip_no = $this->db->escape($trip_no);
        $sql = "SELECT bl.*, bi.bus_name, bi.company, bi.image 
                FROM bus_lists bl
                JOIN bus_info bi ON bl.bus_id = bi.id
                WHERE bl.trip_no = $trip_no";
        
        $result = $this->db->query($sql);
        return $this->db->fetchAssoc($result);
    }
}
?>
