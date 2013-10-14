<!DOCTYPE html>
<html>

<head>

  <meta charset="utf-8" />
  <link rel="stylesheet" href="screen.css" />
  
  <script type="text/javascript" src="jquery.min.js"></script>
  <script type="text/javascript">
  $(document).ready( function() {
  
    $( '.add' ).click( function () {
      $( '.dialogue' ).each( function() { $(this).hide(); } );
    });

    $('#render').click( function() {
        // don't submit any form data, just render the resume
        window.location.href = 'http://resume.cobytamayo.com';
        return false;
    });

    $('#reset').click( function() {
        // don't submit any form data, just reload the URL
        window.location.href = window.location.href;
    });
    
    $( '.addTask' ).click( function() {
      
      var jobid = $( this ).attr( 'name' ).replace( 'addTask_jobid_', '' );
      
      $( '#taskDialogue_'+jobid ).show();
      $( '#addTaskJobid' ).val( jobid );
      
      $( '#taskDialogue_'+jobid+' textarea' ).focus();
    });
    
    $( '#addJob' ).click( function() {
      $( '#jobDialogue' ).show();
      $( '#jobDialogue input[name="add[job][heading]"]' ).focus();
    });
    
    $( '#addExp' ).click( function() {
      $( '#expDialogue' ).show();
      $( '#expDialogue input[name="add[exp][heading]"]' ).focus();
    });
    
    $( '#addSkill' ).click( function() {
      $( '#skillDialogue' ).show();
      $( '#skillDialogue input' ).focus();
    });
    
    //$( '#addTaskForm' ).ajaxForm( { url: 'index.php', type: 'POST' } );
    
    $( '.cancel' ).click( function() {
      $( this ).parents( '.dialogue' ).hide();
    });

    $( '#taskToggle' ).click( function () {
      $( '.task' ).toggle();
      $( '#taskToggle span' ).toggle();
      $( '.addTask' ).toggle();
    });
    
  });
  </script>

</head>

<body>

  <h1>Bob Loblaw's Resume</h1>
  
  <p id="message" class="<?= $error ?>"><?= $message ?></p>
  
  <form action="index.php" method="POST">
  
  <h2>Jobs</h2>
  
  <div class="dialogue item" id="jobDialogue">
    <h3>New Job</h3>
    
    <input type="text" name="add[job][heading]" placeholder="Position, Company" /><br />
    <input type="text" name="add[job][subheading]" placeholder="Place, Time, Cirumstance" />
    
    <p>
      <button type="submit" name="add[type]" value="job">Add</button>
      <button type="button" class="cancel">Cancel</button>
    </p>
  </div>
  
  <ul>
  <?php foreach( $jobs as $jobid => $job ) : ?>
    <li class="item job">
      <input type="text" name="job[<?= $jobid ?>][heading]"
      value="<?= $job['heading'] ?>" /><br />
      <input type="text" name="job[<?= $jobid ?>][subheading]"
      value="<?= $job['subheading'] ?>" />
      
      <?php if( ! empty( $job['tasks'] ) ) : ?>
      <ul>

      <?php foreach( $job['tasks'] as $taskid => $description ) : ?>
        <li>
          <textarea class="task" name="task[<?= $taskid ?>]"
          cols="58" rows="3"><?= $description ?></textarea>
        </li>
      <?php endforeach; ?>

      </ul>
      <?php endif; ?>
  
      <div class="dialogue item" id="taskDialogue_<?= $jobid ?>">
        <h3>New Task</h3>
        <textarea id="addTaskDescription" name="add[task][<?= $jobid ?>]"
        cols="60" rows="6" placeholder="Wadju do?"></textarea>
        <p>
          <button type="submit" name="add[type]" value="task">Add</button>
          <button type="button" class="cancel">Cancel</button>
        </p>
        <input type="hidden" name="add[task][jobid]" value="" id="addTaskJobid" />
      </div>
      
      <button class="add addTask" type="button"
      name="addTask_jobid_<?= $jobid ?>">Add Task</button>
      
    </li>
  <?php endforeach; ?>
  </ul>
  
  <h2>Additional Experience</h2>
  
  <div class="dialogue item" id="expDialogue">
    <h3>New Experience</h3>
    <input id="addExpDescription" name="add[exp][heading]"
    placeholder="Tell a crazy story..." />
    <input id="addExpDescription" name="add[exp][subheading]"
    placeholder="Go on..." />
    <p>
      <button type="submit" name="add[type]" value="exp">Add</button>
      <button type="button" class="cancel">Cancel</button>
    </p>
  </div>
  
  <ul>
  <?php foreach( $exp as $xid => $x ) : ?>
    <li class="item exp">
      <input type="text" name="exp[<?= $xid ?>][heading]" value="<?= $x['heading'] ?>" />
      <input type="text" name="exp[<?= $xid ?>][subheading]"
      value="<?= $x['subheading'] ?>" />
    </li>
  <?php endforeach; ?>
  </ul>
  
  <h2>Skills</h2>
  
  <div class="dialogue item" id="skillDialogue">
    <h3>New Skill</h3>
    <input id="addSkillDescription" name="add[skill][description]"
    placeholder="Describe ur mad skillz..." />
    <p>
      <button type="submit" name="add[type]" value="skill">Add</button>
      <button type="button" class="cancel">Cancel</button>
    </p>
  </div>
  
  <ul>
  <?php foreach( $skills as $skillid => $skill ) : ?>
    <li class="item skill">
      <input type="text" name="skill[<?= $skillid ?>]"
      value="<?= $skill['description'] ?>" />
    </li>
  <?php endforeach; ?>
  </ul>
  
  <div id="controls">
    <p><button type="submit" class="add" name="save">Save Changes</button></p>
    <p><button type="input" id="render">View Rendered</button></p>
    <p><button type="submit" id="reset">Reset</button></p>
    <hr />
    <p><button id="addJob" class="add" type="button">Add Job</button></p>
    <p><button id="addExp" class="add" type="button">Add Experience</button></p>
    <p><button id="addSkill" class="add" type="button">Add Skill</button></p>
    <hr />
    <p><button id="taskToggle" type="button">
      <span style="display: none;">Expand tasks</span>
      <span>Collapse tasks</span>
    </button></p>
  </div>

  <input type="hidden" name="edit" value="1" />
  
  </form>
  
</body>

</html>