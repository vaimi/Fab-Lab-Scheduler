  
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php  echo base_url();?>">Oulu's FabLab</a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?= set_active_nav('home') ?>>
              <?php echo anchor('home', 'Home');?>
            </li>
            <li class="dropdown<?= echo_active_nav_parent('info') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Info<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li<?= set_active_nav('info/floorplan') ?>>
                  <?php echo anchor('info/floorplan', 'Floor plan');?>
                </li>
                <li<?= set_active_nav('info/machines') ?>>
                  <?php echo anchor('info/machines', 'Machines');?>
                </li>
              </ul>
            </li>
            <li class="dropdown<?= echo_active_nav_parent('reservations') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">Reservations<b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li<?= set_active_nav('reservations/active') ?>>
                  <?php echo anchor('reservations/active', 'Active reservations');?>
                </li>
                <li<?= set_active_nav('reservations/reserve') ?>>
                  <?php echo anchor('reservations/reserve', 'Reserve');?>
                </li>
              </ul>
            </li>
            <li<?= set_active_nav('contact') ?>>
              <?php echo anchor('contact', 'Contact us');?>
            </li>
			<?php if ($this->aauth->is_admin()) { ?><li class="dropdown<?= echo_active_nav_parent('admin') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:red">Admin<b class="caret"></b></a>
              <ul class="dropdown-menu">
				<li class="dropdown-header">System control</li>
				<li<?= set_active_nav('admin/moderate_general') ?>>
                  <?php echo anchor('admin/moderate_general', 'General settings');?>
                </li>
				<li class="divider"></li>
				<li class="dropdown-header">Machine control</li>
				<li<?= set_active_nav('admin/moderate_machines') ?>>
                  <?php echo anchor('admin/moderate_general', 'Machines');?>
                </li>
				<li<?= set_active_nav('admin/moderate_timetables') ?>>
                  <?php echo anchor('admin/moderate_general', 'Timetables');?>
                </li>
				<li class="divider"></li>
				<li class="dropdown-header">User control</li>
                <li<?= set_active_nav('admin/moderate_users') ?>>
                  <?php echo anchor('admin/moderate_general', 'Users');?>
                </li>

              </ul>
              <?php } ?>
            </li>
          </ul>
          <div class="navbar-form navbar-right">
          <?php if (!$this->aauth->is_loggedin()) { ?>
            <div class="form-group"></div>
			<?php echo anchor('user/registration', 'Register', array('class' => 'btn btn-primary', 'onclick' => "$('#registerModal').modal('show');return false;"));?>
            <div class="form-group"></div>
			<?php echo anchor('user/login', 'Log in', array('class' => 'btn btn-success', 'onclick' => "$('#loginModal').modal('show');return false;"));?>
		  <?php } else { ?>
		  	<p>Hello <?php echo $this->session->userdata('surname'); ?> <?php echo anchor('user/logout', 'Log out', array('class' => 'btn btn-default'));?>
		  <?php } ?>
          </div>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>
	<?php $this->load->view('modals/login'); ?>
	<?php $this->load->view('modals/register'); ?>