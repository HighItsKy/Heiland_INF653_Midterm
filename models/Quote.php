<?php
class Quote{
    //Declare variables for database items.
    private $conn;
    private $table = 'quotes';

    //Author properties
    public $id;
    public $quote;
    public $author_id;
    public $category_id;

    //Constructor
    public function __construct($db){
        $this->conn = $db;
    }

    //Get quotes
    public function seeQuotes(){
        //Create query
        $query = 'SELECT q.id, q.quote, q.author_id, q.category_id FROM ' . $this->table . ' q';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Execute query
        $stmt->execute();

        return $stmt;
    }

    //Get single author
    public function seeSingleQuote(){
        //Create query
        $query = 'SELECT q.id, q.quote, q.author_id, q.category_id FROM ' . $this->table . ' q WHERE q.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		//Set props
		$this->quote = $row['quote'];
    }

    //Create post
    public function create(){
        //Create query
        $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';

        ///Prepare statement
        $stmt = $this->conn->prepare($query);

        //Clean data
		$this->quote = htmlspecialchars(strip_tags($this->quote));
		$this->author_id = htmlspecialchars(strip_tags($this->author_id));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		$this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(':quote', $this->quote);
		$stmt->bindParam(':author_id', $this->author_id);
		$stmt->bindParam(':category_id', $this->category_id);
		$stmt->bindParam(':id', $this->id);
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
		$query = 'UPDATE ' . $this->table . ' SET quote = :quote, author_id = :author_id, category_id = :category_id WHERE id = :id';

		//Prepare statement
		$stmt = $this->conn->prepare($query);

		//Clean data
		$this->quote = htmlspecialchars(strip_tags($this->quote));
		$this->author_id = htmlspecialchars(strip_tags($this->author_id));
		$this->category_id = htmlspecialchars(strip_tags($this->category_id));
		$this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(':quote', $this->quote);
		$stmt->bindParam(':author_id', $this->author_id);
		$stmt->bindParam(':category_id', $this->category_id);
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