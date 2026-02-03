<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>MIS Report</title>

		<!-- Bootstrap -->
    <link href="{{ URL::asset('assets/bootstrap/dist/css/bootstrap.min.css') }}" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="{{ URL::asset('assets/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <!-- NProgress -->
    <link href="{{ URL::asset('assets/nprogress/nprogress.css') }}" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{ URL::asset('assets/custom.css') }}" rel="stylesheet">
  </head>

  <body class="login">
    <div>
      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
						<h1>Store Login Form</h1>
						{{ Form::open(array('url' => 'store/login','class'=>'form-signin')) }}
						<!-- if there are login errors, show them here -->
						@if($errors->any())
            <div class="alert alert-danger fade in">
              <ul>
  						    @foreach ($errors->all() as $error)
  						        <li>{{ $error }}</li>
  						    @endforeach
  						</ul>
            </div>
						@endif


              <div>

								{{ Form::text('username', null, array(
								    'class' => 'form-control',
								    'id' => '',
								    'placeholder' => 'Username',
								)) }}
              </div>
              <div>
								{{ Form::password('password',array(
								    'class' => 'form-control',
								    'id' => '',
								    'placeholder' => 'Password',
								)) }}
              </div>
              <div style="margin-left:115px;">
								{{ Form::submit('Log in',array(
								    'class' => 'btn btn-success',
								    'id' => '',
								    'placeholder' => '',
								)) }}

								{{ Form::close() }}
              </div>
              <div class="clearfix"></div>


            </form>
          </section>
        </div>


      </div>
    </div>
  </body>
</html>
