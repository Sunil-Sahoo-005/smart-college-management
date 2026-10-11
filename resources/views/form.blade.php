@extends('admin_layout')
@section('content')
    <section class="content">
        <div>
            <a href="https://app.deoalliance.co.in/index.php/home/customerlist">
                <button class="pull-right btn btn-info">Back to List</button>
            </a>
        </div>
        <!--For Registration-->

        <div class="col-lg-12 col-md-6">
            <div class="account_form register">
                <h1 style="font-size: 30px;"><b>Customer Register</b></h1></br>
                </hr>

                <form action="" method="POST">
                    <div class="form-group col-md-6">
                        <lable for="name"><b>Name<span style="color:#FF0000">*</span>:</b></lable>
                        <input class="form-control" name="name" id="name" type="text">
                    </div>


                    <div class="form-group col-md-6">
                        <lable for="email"><b>Email<span style="color:#FF0000">*</span>:</b></lable>
                        <input class="form-control" name="email" id="email" type="text">
                    </div>
                    <div class="form-group col-md-6">
                        <lable for="phone"><b>Mobile Number<span style="color:#FF0000">*</span>:</b></lable>
                        <input class="form-control" name="phone" id="phone" type="text">
                    </div>
                    <div class="form-group col-md-6">
                        <lable for="phone"><b>Refferal Code:</b></lable>
                        <input class="form-control" name="code" id="code" type="text">
                    </div>


                    </br>
                    <div class="form-group col-md-12">
                        <button class="btn btn-success pull-left" name="customerRegister" style="">Register</button>
                    </div></br>
                    <!--                <span>Already have an Account? <a href="https://app.deoalliance.co.in/index.php/auth/login">Login Here!!</a></span>-->

                </form>
            </div>
        </div>
        <!-- registration area end-->
        <!-- /.row (main row) -->

    </section>
@endsection
