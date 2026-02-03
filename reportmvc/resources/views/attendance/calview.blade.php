@extends('layouts.default')

@section('main_container')
@php
if(count($presentlist)>0){
$prelist=explode('@',$presentlist);
$selectdatemonth=explode('-',$prelist[1]);
}
@endphp
    <!-- page content -->
    <div class="right_col" role="main">

      <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
              <div class="x_title">
                <h2 style="padding-bottom: 20px;">Attendance Tracker</h2>
                <div class="clearfix"></div>
                {{ Form::open(array('url' => 'attendancecalview','class'=>'form-signin')) }}
                <div class="col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 20px;padding-bottom: 20px;">
                   <div class="col-md-4 col-sm-4 col-xs-12">
                       <select name="selec_mn" class="form-control">
                          <option value=''>--Select Month--</option>
                          <option value='01' <?php if($selectdatemonth[1]==01) echo "selected" ?>>Janaury</option>
                          <option value='02' <?php if($selectdatemonth[1]==02) echo "selected" ?>>February</option>
                          <option value='03' <?php if($selectdatemonth[1]==03) echo "selected" ?>>March</option>
                          <option value='04' <?php if($selectdatemonth[1]==04) echo "selected" ?>>April</option>
                          <option value='05' <?php if($selectdatemonth[1]==05) echo "selected" ?>>May</option>
                          <option value='06' <?php if($selectdatemonth[1]==06) echo "selected" ?>>June</option>
                          <option value='07' <?php if($selectdatemonth[1]==07) echo "selected" ?>>July</option>
                          <option value='08' <?php if($selectdatemonth[1]==08) echo "selected" ?>>August</option>
                          <option value='09' <?php if($selectdatemonth[1]==09) echo "selected" ?>>September</option>
                          <option value='10' <?php if($selectdatemonth[1]==10) echo "selected" ?>>October</option>
                          <option value='11' <?php if($selectdatemonth[1]==11) echo "selected" ?>>November</option>
                          <option value='12' <?php if($selectdatemonth[1]==12) echo "selected" ?>>December</option>
                      </select>
                   </div>
                   <div class="col-md-4 col-sm-4 col-xs-12">
                        <select name="selec_year" class="form-control">
                             <option value=''>--Select Year--</option>
                             @php
                               for($i=2010;$i<2050;$i++)
                               {
                             @endphp
                                  <option value="<?php echo $i; ?>" <?php if(str_replace("'","",$selectdatemonth[0])==$i) echo "selected" ?>><?php echo $i; ?></option>
                             @php
                               }
                            @endphp
                        </select>
                   </div>
                   <div class="col-md-4 col-sm-4 col-xs-12 text-left">
                       <input type="submit" name="sub" value="Search" class="btn btn-primary">
                   </div>
                 </div>
                   {{ Form::close() }}
                 <div class="clearfix"></div>
              </div>

              <div class="x_content">
                <div class="table-responsive">
                  <table class="table table-striped jambo_table bulk_action sticky-header">
                    <thead>
                      <tr class="headings">
                        <th class="column-title" style="display: table-cell;">Emp</th>
                        <th class="column-title" style="display: table-cell;">1</th>
                        <th class="column-title" style="display: table-cell;">2</th>
                        <th class="column-title" style="display: table-cell;">3 </th>
                        <th class="column-title" style="display: table-cell;">4</th>
                        <th class="column-title" style="display: table-cell;">5</th>
                        <th class="column-title" style="display: table-cell;">6</th>
                        <th class="column-title" style="display: table-cell;">7</th>
                        <th class="column-title" style="display: table-cell;">8</th>
                        <th class="column-title" style="display: table-cell;">9 </th>
                        <th class="column-title" style="display: table-cell;">10</th>
                        <th class="column-title" style="display: table-cell;">11</th>
                        <th class="column-title" style="display: table-cell;">12</th>
                        <th class="column-title" style="display: table-cell;">13</th>
                        <th class="column-title" style="display: table-cell;">14</th>
                        <th class="column-title" style="display: table-cell;">15</th>
                        <th class="column-title" style="display: table-cell;">16</th>
                        <th class="column-title" style="display: table-cell;">17</th>
                        <th class="column-title" style="display: table-cell;">18</th>
                        <th class="column-title" style="display: table-cell;">19</th>
                        <th class="column-title" style="display: table-cell;">20</th>
                        <th class="column-title" style="display: table-cell;">21</th>
                        <th class="column-title" style="display: table-cell;">22</th>
                        <th class="column-title" style="display: table-cell;">23</th>
                        <th class="column-title" style="display: table-cell;">24</th>
                        <th class="column-title" style="display: table-cell;">25</th>
                        <th class="column-title" style="display: table-cell;">26</th>
                        <th class="column-title" style="display: table-cell;">27 </th>
                        <th class="column-title" style="display: table-cell;">28</th>
                        <th class="column-title" style="display: table-cell;">29</th>
                        <th class="column-title" style="display: table-cell;">30</th>
                        <th class="column-title" style="display: table-cell;">31</th>

                      </tr>
                    </thead>

                    <tbody>
                      @php

                      $presentlistarr=explode(',',$prelist[0]);
                      $empdatilasarr=explode(',',$prelist[3]);
                      $begin = new DateTime(str_replace("'","",$prelist[1]));
                      $end = new DateTime(str_replace("'","",$prelist[2]));
                      $end = $end->modify( '+1 day' );
                      $interval =  new DateInterval('P1D');
                      $period = new DatePeriod($begin, $interval, $end);

                      @endphp
                      @foreach ($emplists as $key => $emplist)
                      <tr class="even pointer">
                          <td>{{$emplist->emp_name}}</td>
                          @foreach ($period as $dt)
                          <td>
                            <?php

                                $empattendencelist=$emplist->emp_code.$dt->format("dmY");
                                $date1=$dt->format("Y-m-d");
                                if(in_array($empattendencelist,$presentlistarr))
                                {

                                  if(date('N', strtotime($date1)) >= 7)
                                  {
                                    echo "<a href='#' data-toggle='tooltip' data-placement='top' title='".$emplist->emp_code.'&nbsp;'.$emplist->state.'&nbsp;'.$emplist->designation."'><span style='background-color:#92F293;color:black;font-size:20px;padding: 2px 2px 2px 2px;'>S</span></a>";
                                  }else{
                                    echo "<a href='#' data-toggle='tooltip' data-placement='top' title='".$emplist->emp_code.'&nbsp;'.$emplist->state.'&nbsp;'.$emplist->designation."'><span style='background-color:#92F293;color:black;font-size:20px;padding: 2px 2px 2px 2px;'>P</span></a>";
                                  }
                                }
                                else{
                                  echo "<a href='#' data-toggle='tooltip' data-placement='top' title='".$emplist->emp_code.'&nbsp;'.$emplist->state.'&nbsp;'.$emplist->designation."'><span style='background-color: #FB9EA2;color: black;font-size: 20px;padding: 2px 2px 2px 2px;'>A</span></a>";
                                }
                            ?>
                          </td>
                          @endforeach
                      </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>



              </div>
            </div>
          </div>

    </div>

    <!-- /page content -->
    @include('includes/footer')


@endsection
