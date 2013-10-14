<?php

class Resume {

  const DB_CONNECTION_STR = 'mysql:host=localhost;dbname=resume';
  const DB_USER = 'resume';
  const DB_PASS = 'eyB5BKEnGWULD9h5';
  
  const TYPE_JOB = 1;
  const TYPE_EXP = 2;

  protected $whitelist = array(
    '127.0.0.1',
    '::1',
    '131.191.82.149', // iMac
    '207.66.231.131', // WFH
    '207.66.136.56',  // work iMac
  );
  
  protected $request;
  protected $pdo;
  
  protected $message;

  public function __construct( $request ) {
    $this->request = $request;
    $this->pdo = new PDO( self::DB_CONNECTION_STR, self::DB_USER, self::DB_PASS );
    
    $this->error = false;
  }
  
  
  
  /* CONTROLLER */
  

  public function execute() {
    if( ! empty($this->request['edit']) ) {
      $this->edit();
    } else {
      $this->renderHtmlResume();      
    }
  }
  
  public function edit() {
  
    try {
    
      if( isset( $this->request['add'] ) ) {
        $this->add( $this->request['add'] );
      }
      
      if( isset( $this->request['save'] ) ) {
        $this->save();
      }
      
    } catch( InvalidArgumentException $e ) {
      $this->error = true;
      $this->message = 'Really dude? Really? ' . $e->getMessage();
    }
    
    try{
      
      // User fucked up. Get stuff from the database anyway.
      $resume = $this->fetch();
      
    } catch( Exception $e ) {
      $this->error = true;
      $this->message = 'Something weird happened: ' . $e->getMessage();
    }
    
    $this->display( $resume );
  }
  
  
  
  
  /* VIEW */


  public function renderHtmlResume() {
    $resume = $this->fetch();

    $jobs = $resume['jobs'];
    $exp = $resume['exp'];
    $skills = $resume['skills'];

    include 'print.php';
  }
  
  
  public function display( $resume ) {
  
    // template vars
    $jobs = $resume['jobs'];
    $exp = $resume['exp'];
    $skills = $resume['skills'];
    $error = $this->error ? 'error' : '';
    $message = $this->message;
    
    include 'screen.php';
  }
  
  
  
  
  
  /* MODEL */
  
  
  protected function fetch() {
    $jobs = $this->fetchJobs();
    $skills = $this->fetchSkills();
    $exp = $this->fetchExperiences();
    
    $stuff = array( 'jobs' => $jobs, 'exp' => $exp, 'skills' => $skills );
    
    return $stuff;
  }
  
  protected function fetchJobs() {
  
    $type = self::TYPE_JOB;
    
    $sql = <<<_SQL_
SELECT job.id jobid, heading, subheading, task.id taskid, description
FROM job LEFT JOIN task ON( job.id = task.jobid )
WHERE type = $type ORDER BY orderid ASC
_SQL_;
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $jobs = array();
    while( $row = $sth->fetch( PDO::FETCH_ASSOC ) ) {
      
      $id = $row['jobid'];
      if( ! isset( $jobs[$id] ) ) {
        $jobs[$id] = array(
          'heading'     => $row['heading'],
          'subheading'  => $row['subheading'],
          'tasks'       => array(),
        );
      }
      
      if( ! empty( $row['description'] ) ) {
        $jobs[$id]['tasks'][$row['taskid']] = $row['description'];
      }
    }
    return $jobs;
  }
  
  protected function fetchExperiences() {
  
    $type = self::TYPE_EXP;
    $sql = "SELECT id expid, heading, subheading FROM job WHERE type = $type ORDER BY orderid ASC";
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $skills = array();
    while( $row = $sth->fetch( PDO::FETCH_ASSOC ) ) {
      $skills[$row['expid']] = $row;
    }
    return $skills;
  }
  
  protected function fetchSkills() {
    $sql = 'SELECT id skillid, description FROM skill';
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $skills = array();
    while( $row = $sth->fetch( PDO::FETCH_ASSOC ) ) {
      $skills[$row['skillid']] = $row;
    }
    return $skills;
  }
  
  
  
  /* Save Functions */
  
  
  protected function save() {
    foreach( $this->request['job'] as $jobid => $job ) {
      $this->saveJob( $jobid, $job );
    }
    foreach( $this->request['task'] as $taskid => $description ) {
      $this->saveTask( $taskid, $description );
    }
    foreach( $this->request['exp'] as $xid => $exp ) {
      // experiences stored in job table...
      $this->saveJob( $xid, $exp );
    }
    foreach( $this->request['skill'] as $skillid => $description ) {
      $this->saveSkill( $skillid, $description );
    }
    
    $this->message = 'Saved successfully';
  }
  
  protected function saveJob( $id, $job ) {
    
    if( empty( $job['heading'] ) || empty( $job['subheading'] ) ) {
      throw new InvalidArgumentException( 'Headings cannot be blank' );
    }
  
    $sql = <<<_SQL_
UPDATE job SET
heading = '{$job['heading']}',
subheading = '{$job['subheading']}'
WHERE id = $id
_SQL_;
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
  }
  
  protected function saveTask( $id, $description ) {
  
    $sql = "UPDATE task SET description = '$description' WHERE id = $id";
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
  }
  
  protected function saveSkill( $id, $description ) {
    
    if( empty( $description ) ) {
      throw new InvalidArgumentException( 'Description cannot be blank' );
    }
    
    $sql = "UPDATE skill SET description = '$description' WHERE id = $id";
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
  }
  
  
  
  /* Add Functions */
  
  
  protected function add( $add ) {
    
    switch( $add['type'] ) {
        
      case 'job' :
        $this->addJob( $add['job'], self::TYPE_JOB );
        break;
        
      case 'exp' :
        $this->addJob( $add['exp'], self::TYPE_EXP );
        break;
      
      case 'task' :
        foreach( $add['task'] as $jobid => $description ) {
          if( ! empty( $description ) ) {
            $this->addTask( $jobid, $description );
          }
        }
        break;
        
      case 'skill' :
        $this->addSkill( $add['skill'] );
        break;
    }
  }
  
  /* An "experience" is actually just a job of type 2 */
  protected function addJob( $job, $type ) {
    
    if( empty( $job['heading'] ) || empty( $job['subheading'] ) ) {
      throw new InvalidArgumentException( 'Headings cannot be blank' );
    }
    
    $sql = <<<_SQL_
INSERT INTO job
SET heading = '{$job['heading']}',
subheading = '{$job['subheading']}',
type = $type
_SQL_;
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $added = ( $type == self::TYPE_JOB )
      ? 'Job'
      : 'Experience';
    
    $this->message = "$added added successfully";
  }
  
  protected function addTask( $jobid, $description ) {

    if( empty( $jobid ) ) {
      throw new InvalidArgumentException( 'No jobid!' );
    }

    if( empty( $description ) ) {
      throw new InvalidArgumentException( 'Description cannot be blank' );
    }
  
    $sql = <<<_SQL_
INSERT INTO task
SET jobid = '$jobid',
description = '$description'
_SQL_;
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $this->message = 'Task added successfully';
  }
  
  protected function addSkill( $skill ) {

    if( empty( $skill['description'] ) ) {
      throw new InvalidArgumentException( 'Description cannot be blank' );
    }
    
    $sql = <<<_SQL_
INSERT INTO skill
SET description = '{$skill['description']}'
_SQL_;
    
    $sth = $this->pdo->prepare( $sql );
    $sth->execute();
    
    $this->message = 'Skill added successfully';
  }

  protected function userCanEdit() {
    return in_array( $_SERVER['REMOTE_ADDR'], $this->whitelist );
  }
}



/* RUN THE THING */

$resume = new Resume( $_REQUEST );
$resume->execute();

?>