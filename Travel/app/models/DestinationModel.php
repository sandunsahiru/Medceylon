<<<<<<< HEAD
<?php
// models/Destination.php
class DestinationModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new Database;
    }

    public function getAllDestinations()
    {

        $sql = "SELECT destination_id,destination_name, province, description, image_path FROM traveldestinations";
        $this->pdo->query($sql);
        $this->pdo->execute();
        return $this->pdo->resultSet();
    }


    public function testConnection()
    {
        try {
            $this->pdo->query("SELECT 1"); // Simple query to check connection
            echo "Database connected successfully!";
        } catch (PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }
    }
}
=======
<?php
// models/Destination.php
class DestinationModel
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = new Database;
    }

    public function getAllDestinations()
    {

        $sql = "SELECT destination_id,destination_name, province, description, image_path FROM traveldestinations";
        $this->pdo->query($sql);
        $this->pdo->execute();
        return $this->pdo->resultSet();
    }


    public function testConnection()
    {
        try {
            $this->pdo->query("SELECT 1"); // Simple query to check connection
            echo "Database connected successfully!";
        } catch (PDOException $e) {
            die("Could not connect to the database: " . $e->getMessage());
        }
    }
}
>>>>>>> d7fee2e90c0e8b6767e13b75b1ecae8294eab4cf
