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
        $query = 'SELECT a.id, a.author FROM ' . $this->table . ' a WHERE a.id = ?';

        
        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
        // $this->author  = $row['author'];
       
        /* if($this->author === null){
            echo json_encode(
                array('message' => $this->id . ' Not found'));
                exit();
        }	 */
        
    }

    //Create post
    public function create(){
        /* //Create query
        $query = 'INSERT INTO ' . $this->table . ' (author) SELECT ' . $this->author . 
        ' WHERE NOT EXISTS(
            SELECT a.author FROM ' . $this->table . ' a WHERE a.author = ?)'; 
        print_r($query);  

        ///Prepare statement
        $stmt = $this->conn->prepare($query);

        print_r($stmt);
        
       //Clean data
		$this->author = htmlspecialchars(strip_tags($this->author));
        $this->id = htmlspecialchars(strip_tags($this->id));

		//Bind data
		$stmt->bindParam(1, $this->author);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()){
			return true;
		}
		else{
			printf("Error: %s.\n", $stmt->error);
			return false;
		} */

        //Create query
        //$query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
        
        $temp = $this->author; //Holds the author wanting to be inserted

        $query = 'SELECT a.id, a.author FROM ' . $this->table . ' a WHERE a.author = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->author);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
        // $this->author  = $row['author'];
       
        if($this->author === false){ //If the author is NOT already in the table: 
            $this->author = $temp;

            $query = 'INSERT INTO ' . $this->table . ' (author) VALUES (:author)';
            
            ///Prepare statement
            $stmt = $this->conn->prepare($query);

            //Clean and bind data
            $this->author = htmlspecialchars(strip_tags($this->author));
            $stmt->bindParam(':author', $this->author);

            if($stmt->execute()){
                $this->author = $temp;

                //Finds the newly inserted quote
                $query = 'SELECT authors.id, authors.author FROM authors WHERE authors.author = ?';

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Bind ID
                $stmt->bindParam(1, $this->author);
                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;

                echo(json_encode($this->author));

                return true;
            }
            else{
                printf("Error: %s.\n", $stmt->error);
                return false;
            }
        }	 
        else{ //If the author IS is in the table, do nothing.
            //echo("Test");
            return false;
        }

    }

    //Update post
	public function update()
	{
		$temp = $this->author; //Holds the author wanting to be updated

        $query = 'SELECT a.id FROM ' . $this->table . ' a WHERE a.id = ?';

        //Prepare statement
        $stmt = $this->conn->prepare($query);

        //Bind ID
		$stmt->bindParam(1, $this->id);

		//Execute query
		$stmt->execute();

		$row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->author = $row;
        //print_r($row);
        if($this->author === false){ //If the author is NOT in the table: 
            echo json_encode(
                array('message' => 'author_id Not found'));
            exit();
        }
        else{
            $this->author = $temp;
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
                $this->author = $temp;

                //Finds the newly inserted quote
                $query = 'SELECT authors.id, authors.author FROM authors WHERE authors.author = ?';

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Bind ID
                $stmt->bindParam(1, $this->author);
                //Execute query
                $stmt->execute();

                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                $this->author = $row;

                echo(json_encode($this->author));
                return true;
            }
            else{
                printf("Error: %s.\n", $stmt->error);
                return false;
            }
        }
    }

        //Delete post
        public function delete()
        {
            $temp = $this->id; //Holds the author id wanting to be deleted

            $query = 'SELECT a.id FROM ' . $this->table . ' a WHERE a.id = ?';

            //Prepare statement
            $stmt = $this->conn->prepare($query);

            //Bind ID
            $stmt->bindParam(1, $this->id);

            //Execute query
            $stmt->execute();

            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $this->id = $row;
            //print_r($row);
            if($this->id === false){ //If the author is NOT in the table: 
                echo json_encode(
                    array('message' => 'author_id Not found'));
                exit();
            }
            else{
                $this->id = $temp;

                //Create query
                $query = 'DELETE FROM quotes WHERE author_id = :id';

                //Prepare statement
                $stmt = $this->conn->prepare($query);

                //Clean data
                $this->id = htmlspecialchars(strip_tags($this->id));

                //Bind data
                $stmt->bindParam(':id', $this->id);
            
                if($stmt->execute()){
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
                        $array = array('id' => $this->id);
                        echo(json_encode($array));
                        return true;
                    }
                    else{
                        printf("Error: %s.\n", $stmt->error);
                        return false;
                    }
                }
                else{
                    printf("Error: %s.\n", $stmt->error);
                    return false;
                }	
            }
        }
}
