<x-guest-layout>





<section class="ftco-section">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-md-6 text-center mb-5">

              

				</div>
			</div>
			<div class="row justify-content-center">
              

               
				<div class="col-md-6 col-lg-4">
					<div class="login-wrap p-0">
		      	<form  method="POST" action="{{ route('login') }}" class="signin-form">
                  @csrf
                  <div class="form-group d-md-flex mt-6">
                <img  src="{{ asset('img/favicon/Logo.png') }}" alt="logo" class="col-md-12" >

	            </div>
		      		<div class="form-group">
		      			<input type="email" class="form-control"  id="email" name="email" :value="old('email')" required   placeholder="نام کاربری ( ایمیل )">
		      		</div>
	            <div class="form-group">
	              <input id="password"  type="password"  name="password" required   class="form-control"placeholder="رمز عبور" >
	              <span toggle="#password-field" class="fa fa-fw fa-eye field-icon toggle-password"></span>
	            </div>
	            <div class="form-group">
	            	<button type="submit" class="form-control btn btn-primary submit px-3">ورود به سامانه</button>
	            </div>
	           
	          </form>
	          <div class="social d-flex text-center">
	          	<a href="https://instagram.com/rokaceram.co" class="m-2 rounded"><span class=""></span> instagram</a>
	          	<a href="https://rokaceram.com" class="m-2 rounded"><span class=""></span> website</a>
                
	          </div>
              
		      </div>
				</div>
			</div>
		</div>
	</section>



</x-guest-layout>
