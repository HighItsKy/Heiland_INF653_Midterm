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
        //$query = 'SELECT q.id, q.quote, q.author_id, q.category_id FROM ' . $this->table . ' q';
        $query = 'SELECT q.id, q.quote, 
        authors.author, authors.id AS author_id, 
        categories.category, categories.id AS category_id
        FROM ' . $this->table . ' q 
        INNER JOIN authors ON q.author_id = authors.id 
        INNER JOIN categories ON q.category_id = categories.id';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Execute query
        $stmt->execute();

        return $stmt;
    }

    //Get single author
    public function seeSingleQuote(){
        //Create query
        /* $query = 'SELECT q.id, q.quote, 
        authors.id AS author_id, 
        categories.id AS category_id
        FROM ' . $this->table . ' q 
        INNER JOIN authors ON q.author_id = authors.id 
        INNER JOIN categories ON q.category_id = categories.id 
        WHERE q.id = ?'; */

        $query = 'SELECT q.id, q.quote, 
        authors.author, 
        categories.category
        FROM ' . $this->table . ' q 
        INNER JOIN authors ON q.author_id = authors.id 
        INNER JOIN categories ON q.category_id = categories.id 
        WHERE q.id = ?';
    

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
	    $stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

        //print_r($stmt);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        //print_r($row);
		
        //Set props
		//$this->quote = $row['quote'];
        $this->quote = $row;
    }

    //Create post
    public function create(){
        $temp = $this->quote; //Holds the quote wanting to be inserted

        $query = 'SELECT q.quote FROM ' . $this->table . ' q  WHERE q.quote = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->quote);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->quote = $row;
       
        if($this->quote === false){ //If the quote is NOT already in the table: 
            $this->quote = $temp; 

            //Checks if the quote's author exists in the table already
            $query = 'SELECT authors.id FROM authors WHERE authors.id = ' . $this->author_id;

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->author = $row;

            if($this->author === false){ //If the author is NOT already in the table: 
                echo json_encode(array('message' => 'author_id Not Found'));
                exit();
            }
            else{ //If the author is already in the table, then the category is checked next
                //Checks if the quote's author exists in the table already
                $query = 'SELECT categories.id FROM categories WHERE categories.id = ' . $this->category_id;

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;
                
                if($this->author === false){ //If the category is NOT already in the table: 
                    echo json_encode(array('message' => 'category_id Not Found'));
                    exit();
                }
                else{ //If the category is in the table, then the quote can be inserted into the table
                    $query = 'INSERT INTO ' . $this->table . ' (quote, author_id, category_id) VALUES (:quote, :author_id, :category_id)';
            
                    ///Prepare statement
                    $stmt = $this->conn->prepare($query);

                    //Clean and bind data
                    $this->quote = htmlspecialchars(strip_tags($this->quote));
                    $this->author_id = htmlspecialchars(strip_tags($this->author_id));
                    $this->category_id = htmlspecialchars(strip_tags($this->category_id));
                    $stmt->bindParam(':quote', $this->quote);
                    $stmt->bindParam(':author_id', $this->author_id);
                    $stmt->bindParam(':category_id', $this->category_id);

                    if($stmt->execute()){
                        return true;
                    }
                    else{
                        printf("Error: %s.\n", $stmt->error);
                        return false;
                    }
                }
            }
        }	 
        else{ //If the quote IS is in the table, do nothing.
            //echo("Test");
            return false;
        }
    }

    //Update post
	public function update()
	{
        $tempQuote = $this->quote; //Holds the quote wanting to be updated
        $tempId = $this->id; //Holds the id wanting to be updated
        $tempAuthorId = $this->author_id; //Holds the author id wanting to be updated
        $tempCategoryId = $this->category_id; //Holds the category id wanting to be updated

        //Checks to see if the id exists in the table
        $query = 'SELECT q.id FROM ' . $this->table . ' q  WHERE q.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        
        //Bind ID
		$stmt->bindParam(1, $this->id);

       
		//Execute query
		$stmt->execute();

        //print_r($stmt);
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        //print_r($row);
        $this->id = $row;
        //print_r($this->id);
        
        if($this->id === false){ //If the id is NOT in the table: 
            echo json_encode(array('message' => 'No Quotes Found'));
            exit();
        }
        else{
            $this->quote = $tempQuote; 
            $this->id = $tempId; 
            $this->author_id = $tempAuthorId; 
            $this->category_id = $tempCategoryId; 

            //Checks if the author exists in the table
            $query = 'SELECT authors.id FROM authors WHERE authors.id = ' . $this->author_id;

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->author = $row;

            if($this->author === false){ //If the author is NOT in the table: 
                echo json_encode(array('message' => 'author_id Not Found'));
                exit();
            }
            else{ //If the author is in the table:
                $this->quote = $tempQuote; 
                $this->id = $tempId; 
                $this->author_id = $tempAuthorId; 
                $this->category_id = $tempCategoryId; 
                    
                //Checks if the quote's author exists in the table already
                $query = 'SELECT categories.id FROM categories WHERE categories.id = ' . $this->category_id;

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;
                
                if($this->author === false){ //If the category is NOT already in the table: 
                    echo json_encode(array('message' => 'category_id Not Found'));
                    exit();
                }
                else{ 
                    //The category id, author id, and quote id are all valid. Time to update that quote!
                    $this->quote = $tempQuote; 
                    $this->id = $tempId; 
                    $this->author_id = $tempAuthorId; 
                    $this->category_id = $tempCategoryId; 
                    
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
            }    
        }	
	}

    //Delete post
	public function delete()
	{
        $temp = $this->id; //Holds the quote id wanting to be deleted

        $query = 'SELECT q.id FROM ' . $this->table . ' q  WHERE q.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row;
       
        if($this->id === false){ //If the quote is NOT already in the table: 
            echo json_encode(array('message' => 'No Quotes Found'));
                exit();
        }
        else{
            $this->id = $temp;
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
}