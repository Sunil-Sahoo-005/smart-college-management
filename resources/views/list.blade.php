@extends('admin_layout')
@section('content')
    <section class="content-header">
        <h1>
            Customer Details
            <!--        <small>Add Edit And Delete Customer</small>-->
        </h1>
        <ol class="breadcrumb">
            <li><a href="https://app.deoalliance.co.in/index.php/home"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active">Staff</li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content container-fluid">

        <form name="frm_deleteBanner" id="frm_deleteBanner" action="" method="post">
            <input type="hidden" name="operation" id="operation" value="">
            <input type="hidden" name="country_id" id="country_id" value="">
        </form>

        <script type="text/javascript">
            function deleteRecord(id) {
                $("#operation").val('delete');
                $("#country_id").val(id);
                var conf = confirm("Are you sure want to delete this Record");
                if (conf) {
                    $("#frm_deleteBanner").submit();
                }
            }
        </script>

        <!-- Main content -->
        <section class="content container-fluid">

            <div class="box">
                <div class="box-header">
                    <h3 class="box-title"><a href="https://app.deoalliance.co.in/index.php/auth/customerRegister"><button
                                class="btn btn-primary" data-toggle="modal" data-target="">Add Customer</button></a></h3>
                    <div class="modal fade" id="modal-default-add">

                        <form action="" method="post">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title">Add Customer </h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-group">
                                            <label>Enter Customer Name</label>
                                            <input type="text" class="form-control" name="name" id="name"
                                                placeholder="Enter  Name" required />


                                        </div>

                                        <div class="form-group">
                                            <label>Enter Email</label>
                                            <input type="text" class="form-control" name="email" id="email"
                                                placeholder="Enter  Email" />


                                        </div>

                                        <div class="form-group">
                                            <label>Enter Contact No </label>
                                            <input type="text" class="form-control" name="contact" id="contact"
                                                placeholder="Enter  Contact No" pattern="[1-9]{1}[0-9]{9}" required />


                                        </div>

                                        <div class="form-group">
                                            <label>Enter Address</label>
                                            <textarea class="form-control" placeholder="Address" name="address" id="address"></textarea>


                                        </div>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                placeholder="Enter Customer Gst No" />
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary pull-left " value="addcountry"
                                            name="submit">Add Customer</button>
                                        <button type="button" class="btn btn-default " data-dismiss="modal">Close</button>

                                    </div>
                                </div>
                                <!-- /.modal-content -->
                            </div>

                        </form>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <div class="col-lg-12 col-md-12 ">
                        <form action="https://app.deoalliance.co.in/index.php/home/customerlist/">
                            <div class="row">

                                <div class="form-group col-md-3" id='packagename'>
                                    <label class="text-info">User Id</label>
                                    <input type="text" name="code" id='input' class="form-control"
                                        value="" />
                                </div>
                                <div class="form-group col-md-3" id='packagename'>
                                    <label class="text-info">Contact No</label>
                                    <input type="text" name="contact" id='input' class="form-control"
                                        value="" />
                                </div>
                                <div class="form-group col-md-2" id='packagename'>
                                    <label class="text-info">From Date</label>
                                    <input type="date" name="fdate" id='input' class="form-control"
                                        value="" />
                                </div>
                                <div class="form-group col-md-2" id='packagename'>
                                    <label class="text-info">To Date</label>
                                    <input type="date" name="tdate" id='input' class="form-control"
                                        value="" />
                                </div>
                                <!--                 <div class="form-group col-md-3" id='packagename'>
                            <label class="text-info">Date</label>
                            <input type="date" name="date"  id='input' class="form-control" value=""/>
                        </div>-->





                                <div class="form-group col-md-2" style="margin-top: 20px;">
                                    <button class="btn btn-primary">Search</button>
                                </div>

                            </div>
                        </form>
                    </div>

                    <table id="example" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <!--                    <th> SL.NO</th>-->
                                <th> User Id</th>
                                <th> Name</th>
                                <th> Email</th>
                                <th> Contact No</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>


                            <tr>

                                <td>DAPL311</td>
                                <td>Jasim Akram</td>
                                <td>fancydresses751@gmail.com</td>
                                <td>8093270938</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/431"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/431"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>



                            <tr>

                                <td>DAPL310</td>
                                <td>satyabrata sahoo</td>
                                <td>ssahoo485@gmail.com</td>
                                <td>9938304836</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/430"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/430"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>



                            <tr>

                                <td>DAPL309</td>
                                <td>gobardhanpaida</td>
                                <td>gobardhanpaida6@gmail.com</td>
                                <td>8917532203</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/429"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/429"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>



                            <tr>

                                <td>DAPL308</td>
                                <td>pranaya Kumar nanda</td>
                                <td>pranayakumarnanda@gmail.com</td>
                                <td>9439389153</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/428"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/428"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>



                            <tr>

                                <td>DAPL307</td>
                                <td>Bimal kumar sahoo</td>
                                <td>sahoobimal001@gmail.com</td>
                                <td>9853082837</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/427"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/427"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>





                            <tr>

                                <td>DAPL212</td>
                                <td>Yoshabanta mahanta</td>
                                <td>yoshabantamahanta2018@gmail.com</td>
                                <td>9437613637</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/319"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/319"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>



                            <tr>

                                <td>DAPL211</td>
                                <td>Pabitra Sahoo</td>
                                <td>pabitraksahoo706@gmail.com</td>
                                <td>9937918973</td>




                                <td> <a href="https://app.deoalliance.co.in/index.php/home/customeredit/318"
                                        class="btn btn-social-icon btn-primary"><i class="fa fa-edit"></i></a>
                                    <a href="https://app.deoalliance.co.in/index.php/home/customerdelete/318"
                                        onclick="return deletechecked();"
                                        class="btn btn-social-icon btn-primary remove"><i class="fa fa-trash"></i></a>
                                </td>

                                </td>
                            </tr>

                        </tbody>
                    </table>







                    <div class="modal fade" id="modal-default-edit1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Party</h4>
                                </div>
                                <div class="modal-body">

                                    <form action="" method="post" enctype="multipart/form-data" name="frm"
                                        id="frm">

                                        <input type="hidden" class="form-control" name="pagesId" value="1">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="Samir Sahoo " />
                                        <p></p>
                                        <label>Customer E-Mail</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="1984samirsahoo@gmail.com " />
                                        <p></p>
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="9658437202 " pattern="[1-9]{1}[0-9]{9}" />
                                        <p></p>
                                        <label>Address</label>
                                        <textarea class="form-control" name="address" id="address">   </textarea>
                                        <p></p>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                value="" />
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" type="submit" name="submit"
                                        value="editcountry">Update Customer</button>

                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal fade" id="modal-default-edit12">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Party</h4>
                                </div>
                                <div class="modal-body">

                                    <form action="" method="post" enctype="multipart/form-data" name="frm"
                                        id="frm">

                                        <input type="hidden" class="form-control" name="pagesId" value="12">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="raja " />
                                        <p></p>
                                        <label>Customer E-Mail</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="raja@gmail.com " />
                                        <p></p>
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="9776828659 " pattern="[1-9]{1}[0-9]{9}" />
                                        <p></p>
                                        <label>Address</label>
                                        <textarea class="form-control" name="address" id="address"> werfwwfw  </textarea>
                                        <p></p>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                value="234" />
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" type="submit" name="submit"
                                        value="editcountry">Update Customer</button>

                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal fade" id="modal-default-edit13">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Party</h4>
                                </div>
                                <div class="modal-body">

                                    <form action="" method="post" enctype="multipart/form-data" name="frm"
                                        id="frm">

                                        <input type="hidden" class="form-control" name="pagesId" value="13">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="Jitesh " />
                                        <p></p>
                                        <label>Customer E-Mail</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="jiteshdas@gmail.com " />
                                        <p></p>
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="9007390995 " pattern="[1-9]{1}[0-9]{9}" />
                                        <p></p>
                                        <label>Address</label>
                                        <textarea class="form-control" name="address" id="address"> Bhubaneswar  </textarea>
                                        <p></p>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                value="" />
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" type="submit" name="submit"
                                        value="editcountry">Update Customer</button>

                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal fade" id="modal-default-edit14">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Party</h4>
                                </div>
                                <div class="modal-body">

                                    <form action="" method="post" enctype="multipart/form-data" name="frm"
                                        id="frm">

                                        <input type="hidden" class="form-control" name="pagesId" value="14">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="Rakesh Patra  " />
                                        <p></p>
                                        <label>Customer E-Mail</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="rakeshpatra436@gmail.com " />
                                        <p></p>
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="7504194663 " pattern="[1-9]{1}[0-9]{9}" />
                                        <p></p>
                                        <label>Address</label>
                                        <textarea class="form-control" name="address" id="address"> plot no 163/A,near water tank  </textarea>
                                        <p></p>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                value="21AGCPP6442R1Z1" />
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" type="submit" name="submit"
                                        value="editcountry">Update Customer</button>

                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>

                    <div class="modal fade" id="modal-default-edit15">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span></button>
                                    <h4 class="modal-title">Edit Party</h4>
                                </div>
                                <div class="modal-body">

                                    <form action="" method="post" enctype="multipart/form-data" name="frm"
                                        id="frm">

                                        <input type="hidden" class="form-control" name="pagesId" value="15">
                                        <label>Customer Name</label>
                                        <input type="text" class="form-control" name="name" id="name"
                                            value="chitaranjan " />
                                        <p></p>
                                        <label>Customer E-Mail</label>
                                        <input type="text" class="form-control" name="email" id="email"
                                            value="chitaranjan.sahoo@turtlecap.com " />
                                        <p></p>
                                        <label>Contact No</label>
                                        <input type="text" class="form-control" name="contact" id="contact"
                                            value="7788822584 " pattern="[1-9]{1}[0-9]{9}" />
                                        <p></p>
                                        <label>Address</label>
                                        <textarea class="form-control" name="address" id="address"> bbsr  </textarea>
                                        <p></p>

                                        <div class="form-group">
                                            <label>Customer GST No</label>
                                            <input type="text" class="form-control" name="gst" id="gst"
                                                value="00120" />
                                        </div>

                                </div>
                                <div class="modal-footer">
                                    <button class="btn btn-default pull-left" type="submit" name="submit"
                                        value="editcountry">Update Customer</button>

                                    </form>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>



                </div>
                <!-- /.box-body -->
            </div>

        </section>



    </section>
@endsection
