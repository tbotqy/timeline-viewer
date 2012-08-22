<!-- .error-inner -->
<div class="error-inner error404">
  
  <!-- .area-alert -->
  <div class="wrap area-alert">
    
    <div class="alert alert-warning not-found">
      <h3><i class="icon-info-sign"></i>404 : Page Not Found :-(</h3>
    </div>

    <p>
      <?php printf(
                                                                __d('cake', 'The requested address %s was not found on this server.'),
                                                                "<strong>'{$url}'</strong>"
	    ); ?>
    </p>
    
  </div>
  <!-- /.wrap.area-alert -->

</div>
<!-- /.error-inner -->
