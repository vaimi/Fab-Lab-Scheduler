  
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="<?php  echo base_url();?>"><?=$this->lang->line('fablab_menu_title');?></a>
        </div>
        <div class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li<?= set_active_nav('home') ?>>
              <?php echo anchor('home', $this->lang->line('fablab_menu_home_title'));?>
            </li>
            <li class="dropdown<?= echo_active_nav_parent('info') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$this->lang->line('fablab_menu_info_title');?><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li<?= set_active_nav('info/machines') ?> >
                  <?php echo anchor('info/machines', $this->lang->line('fablab_menu_info_machines'));?>
                </li>
                <li<?= set_active_nav('info/people') ?> >
                  <?php echo anchor('info/people', $this->lang->line('fablab_menu_info_people'));?>
                </li>
              </ul>
            </li>
            <li class="dropdown<?= echo_active_nav_parent('reservations') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?=$this->lang->line('fablab_menu_reservations_title');?><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li<?= set_active_nav('reservations/basic_schedule') ?>>
                  <?php echo anchor('reservations/basic_schedule', $this->lang->line('fablab_menu_reservations_basic'));?>
                </li>
                <?php
                if($this->aauth->is_loggedin())
                {
                  echo "<li " . set_active_nav('reservations/active') . ">";
                  echo anchor('reservations/active', $this->lang->line('fablab_menu_reservations_active'));
                  echo "</li>";
                  echo "<li " . set_active_nav('reservations/reserve') . ">";
                  echo anchor('reservations/reserve', $this->lang->line('fablab_menu_reservations_reserve'));
                  echo "</li>";
                }
                else
                {
                  echo "<li " . set_active_nav('reservations/active') . " class=\"disabled\">";
                  echo anchor('#', $this->lang->line('fablab_menu_reservations_active'), 'class="menu-disabled"');
                  echo "</li>";
                  echo "<li " . set_active_nav('reservations/reserve') . " class=\"disabled\">";
                  echo anchor('#', $this->lang->line('fablab_menu_reservations_reserve'), 'class="menu-disabled"');
                  echo "</li>";
                }
                ?>
              </ul>
            </li>
            <li<?= set_active_nav('contact') ?>>
              <?php echo anchor('contact', $this->lang->line('fablab_menu_contact_title'));?>
            </li>
			<?php if ($this->aauth->is_admin()) { ?><li class="dropdown<?= echo_active_nav_parent('admin') ?>">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" style="color:red"><?=$this->lang->line('fablab_menu_admin_title');?><b class="caret"></b></a>
              <ul class="dropdown-menu">
				<li class="dropdown-header">System control</li>
				<li<?= set_active_nav('admin/moderate_general') ?>>
                  <?php echo anchor('admin/moderate_general', $this->lang->line('fablab_menu_admin_general'));?>
                </li>
				<li class="divider"></li>
				<li class="dropdown-header">Machine control</li>
				<li<?= set_active_nav('admin/moderate_machines') ?>>
                  <?php echo anchor('admin/moderate_machines', $this->lang->line('fablab_menu_admin_machines'));?>
                </li>
				<li<?= set_active_nav('admin/moderate_timetables') ?>>
                  <?php echo anchor('admin/moderate_timetables', $this->lang->line('fablab_menu_admin_timetables'));?>
                </li>
                </li>
                  <li<?= set_active_nav('admin/moderate_reservations') ?>>
                  <?php echo anchor('admin/moderate_reservations', $this->lang->line('fablab_menu_admin_reservations'));?>
                </li>
				<li class="divider"></li>
				<li class="dropdown-header">User control</li>
                <li<?= set_active_nav('admin/moderate_users') ?>>
                  <?php echo anchor('admin/moderate_users', $this->lang->line('fablab_menu_admin_users'));?>
                </li>
                <li<?= set_active_nav('admin/groups') ?>>
                  <?php echo anchor('admin/groups', $this->lang->line('fablab_menu_admin_groups'));?>
                </li>
                <li<?= set_active_nav('admin/send_emails') ?>>
                  <?php echo anchor('admin/send_emails', $this->lang->line('fablab_menu_admin_email'));?>
                </li>

              </ul>
              <?php } ?>
            </li>
          </ul>
          <div class="navbar-form navbar-right btn-toolbar">
          <?php if (!$this->aauth->is_loggedin()) 
		  {
			echo anchor('user/registration', $this->lang->line('fablab_menu_register'), array('class' => 'btn btn-primary', 'onclick' => "$('#registerModal').modal('show');return false;"));
			echo anchor('user/login', $this->lang->line('fablab_menu_login'), array('class' => 'btn btn-success', 'onclick' => "$('#loginModal').modal('show');return false;"));
		  } 
		  else 
		  {
			echo '<a type="button" class="btn btn-primary" href=' . base_url('user/profile') . '>';
			echo '<span class="glyphicon glyphicon-user" aria-hidden="true"></span> ' . $this->session->userdata('first_name') . " " . $this->session->userdata('surname');
			echo '</a>';
			echo anchor('user/logout', $this->lang->line('fablab_menu_logout'), array('class' => 'btn btn-default'));
		  }
		   ?>
          </div>
        </div>
        <!--/.navbar-collapse -->
      </div>
    </div>
    <?php if (!$this->aauth->is_loggedin()) 
    {
    	$this->load->view('modals/login');
    	$this->load->view('modals/register'); 
    }?>
