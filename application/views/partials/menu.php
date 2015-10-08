  
  <body>
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">Oulu's FabLab</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active">
              <?php echo anchor('home', 'Home');?>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Info<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                  <?php echo anchor('info/floorplan', 'Floor plan');?>
                </li>
                <li>
                  <?php echo anchor('info/machines', 'Machines');?>
                </li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reservations<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li>
                  <?php echo anchor('reservations/active', 'Active reservations');?>
                </li>
                <li>
                  <?php echo anchor('reservations/reserve', 'Reserve');?>
                </li>
              </ul>
            </li>
            <li>
              <?php echo anchor('contact', 'Contact us');?>
            </li>
          </ul>
          <form class="navbar-form navbar-right">
            <div class="form-group"></div>
			<?php echo anchor('register', 'Register', 'class="btn btn-primary"');?>
            <div class="form-group"></div>
			<?php echo anchor('login', 'Log in', 'class="btn btn-success"');?>
          </form>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>