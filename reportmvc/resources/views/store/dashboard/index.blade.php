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
         <div class="row">
           <div class="top_nav">
              <div>
               <nav>
                 <ul class="nav navbar-left"><li><img src="http://salesmpower.acedns.in/storecreate/style/img/acednslogo.gif"></li></ul>
                 <ul class="nav navbar-right"><li><a href="{{ url('store/logout') }}">Log Out</a></li></ul>
               </nav>
              </div>
           </div>
           <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2>ACEdns Store</h2>
                <a href="{{url('/store/createstore')}}" class="btn btn-success pull-right">Add New Store</a>
                <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action">
                    <thead>
                      <tr class="headings">
                        <th class="column-title">Company Name </th>
                        <th class="column-title">Nick Name</th>
                        <th class="column-title">Address</th>
                        <th class="column-title">Phone Number</th>
                        <th class="column-title">Email</th>
                        <th class="column-title no-link last"><span class="nobr">Action</span></th>
                      </tr>
                    </thead>

                    <tbody>
                      @foreach ($storelists as $key => $storelist)
                        <tr class="even pointer">
                          <td class=" ">{{ $storelist->name }}</td>
                          <td class=" ">{{ $storelist->nick_name }}</td>
                          <td class=" ">{{ $storelist->address }}</td>
                          <td class=" ">{{ $storelist->phone_no }}</td>
                          <td class=" ">{{ $storelist->email }}</td>
                          <td class=" "><a href="{{ url('/store/createuser/'.$storelist->user_id) }}">Edit</a></td>
                          </td>
                        </tr>
                     @endforeach
                    </tbody>
                  </table>
                </div>
                <div class="panel-footer">
                  <div class="row">
                    <div class="col col-xs-8">
                      {{ $storelists->links() }}
                    </div>
                  </div>
                </div>


              </div>
            </div>
          </div>
         </div>
  </body>
</html>
