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
        $tempQuote = $this->quote; //Holds the quote wanting to be inserted
        $tempAuthorId= $this->author_id; //Holds the author id.
        $tempCategoryId = $this->category_id; //Holds the category_id.

        //Checks if the quote's author exists in the table already
        $query = 'SELECT authors.id, authors.author FROM authors WHERE authors.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
        $stmt->bindParam(1, $this->author_id);

        //Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author_id = $row;

        if($this->author_id === false){ //If there is no author already in the table: 
            echo json_encode(array('message' => 'author_id Not Found'));
            exit();
        }
        else{ //If the author is already in the table, then the category is checked 
            $this->quote = $tempQuote;
            $this->author_id = $tempAuthorId;
            $this->category_id = $tempCategoryId;

            //Checks if the quote's author exists in the table already
            $query = 'SELECT categories.id, categories.category FROM categories WHERE categories.id = ?';

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Bind ID
            $stmt->bindParam(1, $this->category_id);

            //Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->category_id = $row;
            
            if($this->category_id === false){ //If the category is NOT already in the table: 
                echo json_encode(array('message' => 'category_id Not Found'));
                exit();
            }
            else{ //Now, it checks to see if the quote is already in the table.
                $this->quote = $tempQuote;
                $this->author_id = $tempAuthorId;
                $this->category_id = $tempCategoryId;

                $query = 'SELECT q.quote, q.id FROM ' . $this->table . ' q  WHERE q.quote = ?';

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Bind ID
                $stmt->bindParam(1, $this->quote);

                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->quote = $row;
            
                if($this->quote === false){ //If the quote is NOT already in the table: 
                    $this->quote = $tempQuote;
                    $this->author_id = $tempAuthorId;
                    $this->category_id = $tempCategoryId;

                    //The new quote is inserted
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

                   
                    if($stmt->execute()){ //If the quote was successfully added:
                        $this->quote = $tempQuote; 
                        $this->author_id = $tempAuthorId;
                        $this->category_id = $tempCategoryId;

                        //Find the newly inserted quote
                        $query = 'SELECT quotes.id, quotes.quote, quotes.author_id, quotes.category_id FROM quotes WHERE quotes.quote = ?';

                        //Prepare statement
                        $stmt = $this->conn->prepare($query);

                        //Bind ID
                        $stmt->bindParam(1, $this->quote);
                        //Execute query
                        $stmt->execute();

                        
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        $this->quote = $row;
                        //$this->id = $row['id'];
                        //$array = array('id' => $this->id, 'quote' => $this->quote, 'author_id' => $this->author_id, 'category_id' => $this->category_id);
                        //echo(json_encode($array));
                        echo(json_encode($this->quote)); //Output the new quote

                        //echo(json_encode($this->quote));

                        return true;
                    }
                    else{
                        return false;
                    }
                }
            }
        }
    }

    //Update post
	public function update()
	{
        $tempQuote = $this->quote; //Holds the quote wanting to be updated
        $tempAuthorId = $this->author_id; //Holds the author id wanting to be updated
        $tempCategoryId = $this->category_id; //Holds the category id wanting to be updated
        $tempId = $this->id;

        //Checks if the author exists in the table
        $query = 'SELECT authors.id FROM authors WHERE authors.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
        $stmt->bindParam(1, $this->author_id);

        //Execute query
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author_id = $row;

        if($this->author_id === false){ //If the author id is NOT in the table: 
            echo json_encode(array('message' => 'author_id Not Found'));
            exit();
        }
        else{ //If the author is in the table:
            $this->quote = $tempQuote; 
            $this->author_id = $tempAuthorId; 
            $this->category_id = $tempCategoryId; 
            $this->id = $tempId; 
                
            //Checks if the category exists in the table already
            $query = 'SELECT categories.id FROM categories WHERE categories.id = ?';

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Bind ID
            $stmt->bindParam(1, $this->category_id);

            //Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->category_id = $row;
            
            if($this->category_id === false){ //If the category is NOT already in the table: 
                echo json_encode(array('message' => 'category_id Not Found'));
                exit();
            }
            else{ //If the category exists in the table
                $this->quote = $tempQuote; 
                $this->author_id = $tempAuthorId; 
                $this->category_id = $tempCategoryId; 
                $this->id = $tempId; 
                
                //Checks to see if the quote id exists in the table
                $query = 'SELECT q.id FROM ' . $this->table . ' q  WHERE q.id = ?';

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Bind ID
                $stmt->bindParam(1, $this->id);

            
                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->id = $row;
        
                if($this->id === false){ //If the id is NOT in the table: 
                    echo json_encode(array('message' => 'No Quotes Found'));
                    exit();
                }
                else{
                    //The category id, author id, and quote id are all valid. Time to update that quote!
                    $this->quote = $tempQuote; 
                    $this->author_id = $tempAuthorId; 
                    $this->category_id = $tempCategoryId; 
                    $this->id = $tempId; 
                    
                    //Update the quote
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

                    if($stmt->execute()){ //If the quote was successfully updated
                        $this->quote = $tempQuote; 
                        $this->author_id = $tempAuthorId; 
                        $this->category_id = $tempCategoryId; 
                        $this->id = $tempId; 

                        //Finds the newly updated quote
                        $query = 'SELECT quotes.id, quotes.quote, quotes.author_id, quotes.category_id FROM quotes WHERE quotes.quote = ?';

                        //Prepare statement
                        $stmt = $this->conn->prepare($query);

                        //Bind ID
                        $stmt->bindParam(1, $this->quote);
                        
                        //Execute query
                        $stmt->execute();

                        $row = $stmt->fetch(PDO::FETCH_ASSOC);

                        $this->quote = $row;

                        echo(json_encode($this->quote)); //Output the row corresponding to that quote
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

        //Looks to see if the quote is in the table
        $query = 'SELECT q.id FROM ' . $this->table . ' q  WHERE q.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->id = $row;
       
        if($this->id === false){ //If the quote is NOT in the table: 
            echo json_encode(array('message' => 'No Quotes Found'));
                exit();
        }
        else{ //If the quote is in the table
            $this->id = $temp;

            //That quote is now deleted
            $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Clean data
            $this->id = htmlspecialchars(strip_tags($this->id));

            //Bind data
            $stmt->bindParam(':id', $this->id);
            
            if($stmt->execute()){ //If the deletion was successful, then the id of the quote is outputted
                $array = array('id' => $this->id);
                echo(json_encode($array));
                return true;
            }
            else{
                printf("Error: %s.\n", $stmt->error);
                return false;
            }
        }
	}
}