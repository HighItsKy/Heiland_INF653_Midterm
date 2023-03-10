<?php
class Author{
    //Declare variables for database items.
    private $conn;
    private $table = 'authors';

    //Author properties
    public $id;
    public $author;

    //Constructor
    public function __construct($db){
        $this->conn = $db;
    }

    //Get authors
    public function seeAuthors(){
        //Create query
        $query = 'SELECT a.id, a.author FROM ' . $this->table . ' a';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Execute query
        $stmt->execute();

        return $stmt;
    }

    //Get single author
    public function seeSingleAuthor(){
        //Create query
        $query = 'SELECT a.id, a.author FROM ' . $this->table . ' a WHERE a.id = ? LIMIT 0,1';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//Set props
		$this->author = $row['author'];
    }

    //Create post
    public function create(){
        //Create query
        $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';

        ///Prepare statement
        $stmt = $this->conn->prepare($query);

        //Clean and bind data
        $this->author = htmlspecialchars(strip_tags($this->author));
        $stmt->bindParam(':author', $this->author);

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
		$query = 'UPDATE ' . $this->table . ' SET author = :author WHERE id = :id';

		//Prepare statement
		$stmt = $this->conn->prepare($query);

		//Clean data
		$this->author = htmlspecialchars(strip_tags($this->author));
        $this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(':author', $this->author);
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