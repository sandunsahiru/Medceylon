<?php
// models/TravelPlanModel.php


class TravelPlan
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new Database;
    }

    public function getAllTravelPlans()
    {
        $sql = "SELECT d.destination_name, d.province, d.image_path, t.stay_duration, t.check_in, t.check_out, t.travel_plan_id, t.destination_id 
        FROM traveldestinations d JOIN travel_plans t 
        ON d.destination_id = t.destination_id";

        $this->pdo->query($sql);
        $this->pdo->execute();
        return $this->pdo->resultSet(); 
        
    }

    public function addTravelPlan($destination_id, $startDate, $endDate){
        $sql = "INSERT INTO travel_plans (destination_id, check_in, check_out, stay_duration)
        VALUES (?, ?, ?, DATEDIFF(?, ?))";

        $this->pdo->query($sql);

        $this->pdo->bind(1, $destination_id);
        $this->pdo->bind(2, $startDate);
        $this->pdo->bind(3, $endDate);
        $this->pdo->bind(4, $endDate);            
        $this->pdo->bind(5, $startDate);
        
        $this->pdo->execute();

    }

    public function deleteTravelPlan($travel_plan_id) {
        $sql = "DELETE FROM travel_plans WHERE travel_plan_id = :travel_plan_id";
    
        $this->pdo->query($sql);
        $this->pdo->bind(':travel_plan_id', $travel_plan_id, PDO::PARAM_INT);
    
        if ($this->pdo->execute()) {
            return true;
        } else {
            // Log any errors for debugging
            error_log("Database error: " . json_encode($this->pdo->stmt->errorInfo()));
            return false;
        }
    }
    
    

    public function editTravelPlan($travel_plan_id, $startDate, $endDate){
        $sql = "UPDATE travel_plans 
                SET check_in = ?, 
                    check_out = ?, 
                    stay_duration = DATEDIFF(?, ?) 
                WHERE travel_plan_id = ?";
    
        $this->pdo->query($sql);
        $this->pdo->bind(1, $startDate);
        $this->pdo->bind(2, $endDate);
        $this->pdo->bind(3, $endDate);
        $this->pdo->bind(4, $startDate);
        $this->pdo->bind(5, $travel_plan_id);
    
        if (!$this->pdo->execute()) {
            error_log("Database error: " . json_encode($this->pdo->errorInfo()));
            return false;
        }
        return true;
    }
    

}