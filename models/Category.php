<?php
class Category{
    //Declare variables for database items.
    private $conn;
    private $table = 'categories';

    //Category properties
    public $id;
    public $category;

    //Constructor
    public function __construct($db){
        $this->conn = $db;
    }

    //Get categories
    public function seeCategories(){
        //Create query
        $query = 'SELECT c.id, c.category FROM ' . $this->table . ' c';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Execute query
        $stmt->execute();

        return $stmt;
    }

    //Get single cetegory
    public function seeSingleCategory(){
        //Create query
        $query = 'SELECT c.id, c.category FROM ' . $this->table . ' c WHERE c.id = ?';
        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//Set props
      //  $this->category = $row['category'];
        $this->category = $row;
/* 
        if($this->category === null){
            echo json_encode(
                array('message' => $this->id . ' Not found'));
                exit();
        }	 */
    }

    //Create post
    public function create(){
        //Create query
        $query = 'INSERT INTO ' . $this->table . ' (category) VALUES (:category)';

        ///Prepare statement
        $stmt = $this->conn->prepare($query);

        //Clean and bind data
        $this->category = htmlspecialchars(strip_tags($this->category));
        $stmt->bindParam(':category', $this->category);

        if($stmt->execute()){
            return true;
        }
        else{
            printf("Error: %s.\n", $stmt->error);
            return false;
        }
    }

    //Update post
	public function update()
	{
		//Create query
		$query = 'UPDATE ' . $this->table . ' SET category = :category WHERE id = :id';

		//Prepare statement
		$stmt = $this->conn->prepare($query);

		//Clean data
		$this->category = htmlspecialchars(strip_tags($this->category));
        $this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(':category', $this->category);
		$stmt->bindParam(':id', $this->id);
		
		if($stmt->execute()){
			return true;
		}
		else{
			printf("Error: %s.\n", $stmt->error);
			return false;
		}
	}

    //Delete post
	public function delete()
	{
		//Create query
		$query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

		//Prepare statement
		$stmt = $this->conn->prepare($query);

		//Clean data
		$this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(':id', $this->id);
		
		if($stmt->execute()){
			return true;
		}
		else{
			printf("Error: %s.\n", $stmt->error);
			return false;
		}
	}
}