<html>

<head>
  <link rel="Stylesheet" href="print.css" />
</head>

<body>
  <div id="wrapper">

  <header>
    <h1>Coby Tamayo</h1>

    <p id="contact">820 S Cushman Ave &middot;
      Tacoma, WA 98405 &middot;
      coby.tamayo@gmail.com &middot;
      (253) 222 9139
    </p>
  </header>

  <h2>Education</h2>

  <p>Bachelor of Science in Computer Science</p>
  <p>University of Puget Sound, Tacoma, WA May 2011</p>
  <p>GPA: 3.05/4.0 overall</p>
  
  <h2>Experience</h2>
  
  <?php foreach( $jobs as $job ) : ?>

  <div class="job item">
    <div class="heading"><?= $job['heading'] ?></div>
    <div class="subheading"><?= $job['subheading'] ?></div>

    <ul>
      <?php foreach( $job['tasks'] as $task ) : ?>
      <li><?= $task ?></li>
      <?php endforeach; ?>
    </ul>

  </div>

  <?php endforeach; ?>

  <h2>Additional Experience</h2>

  <?php foreach( $exp as $x ) : ?>

  <div class="exp item">
    <div class="heading"><?= $x['heading'] ?></div>
    <div class="subheading"><?= $x['subheading'] ?></div>
  </div>

  <?php endforeach; ?>

  <h2>Skills</h2>

  <ul>
  <?php foreach( $skills as $skill ) : ?>
  <li><?= $skill['description'] ?></li>
  <?php endforeach; ?>
  </ul>
  
  </div>

  <?php if( $this->userCanEdit() ) : ?>
  <a id="edit" href="?edit=1">Edit</a>
<?php else : echo $_SERVER['REMOTE_ADDR']; ?>

  <?php endif; ?>

</body>

</html>