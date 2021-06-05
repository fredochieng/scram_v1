<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;

class AuthenticationController extends Controller
{
    public function show_login_form()
    {
        return view('auth.login');
    }

    // public function show_forgot_form()
    // {
    //     return view('authentication.forgot-password');
    // }

    // public function show_response_form()
    // {
    //     return view('authentication.success');
    // }


    public function user_login(Request $request)
    {
        $remember = ($request->has('remember')) ? true : false;

        $userdata = array(
            'email'     => $request->input('email'),
            'password'  => $request->input('password'),
        );

       // dd($userdata);

         // attempt to do the login
         if (Auth::attempt($userdata)) {

            dd("Sucess");
             $user = auth()->user();
             /** Get the user role for redirection */
             $user = User::where('email', '=', $request->input('email'))->first();

             $user_id = $user->id;

            // $user_role = DB::table('model_has_roles')->where('model_id', '=', $user_id)->first();
            // $role_id = $user_role->role_id;

            // if ($role_id == 1) {

            //      toastr()->success('Login successful');
               return redirect('/home');


         } else {
             dd("Failure");
            //   toastr()->error('Incorrect email or password');
             return back();
         }
    }

    public function reset_password(Request $request)
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();

        if (empty($user)) {
            $data['icon'] = "close";
            $data['status'] = "error";
            $data['title'] = " Password Reset";
            $data['message'] = "The email address you entered is not registered. Please try again";
            return view('authentication.success')->with($data);
        } else {

            $reset_token = sha1(time());
            $save_reset_array = array(
                'email' => $email,
                'token' => $reset_token
            );

            $save = DB::table('password_resets')->insert($save_reset_array);

            $app_url = ENV('APP_URL');

            $name = $user->name;
            $email = $user->email;
            $title = 'Reset Password';
            $message_body = $app_url . ('/reset-my-password/&token=' . $reset_token);

            $send_mail = Mail::to($email)->send(new ResetPassword($name, $title, $message_body));
            $data['icon'] = "check";
            $data['status'] = "thank";
            $data['title'] = 'Password Reset';
            $data['message'] = "We have sent the password reset instructions to your email address. Kindly check your email address and reset your password";

            return view('authentication.success')->with($data);
        }
    }

    public function reset($token)
    {
        $token_data = DB::table('password_resets')->where('token', $token)->first();

        if (!empty($token_data)) {
            $email = $token_data->email;

            return redirect('/new-password/&token=' . $token);
        } else {
        }
        // dd($token);
    }

    public function new_password(Request $request)
    {
        $data['token'] = $request->token;
        $data['user_email'] = DB::table('password_resets')->where('token', $data['token'])->first()->email;

        return view('authentication.reset-password')->with($data);
    }

    public function password_reset(Request $request)
    {
        $new_pass = $request->input('new_password');
        $confirm_pass = $request->input('confirm_password');
        $email = $request->email;

        if ($new_pass == $confirm_pass) {
            $user_pass = array(
                'password' => Hash::make($new_pass)
            );

            $update_password = User::where('email', $email)->update($user_pass);
            $app_url = ENV("APP_URL");
            $message_body = $app_url . ('/signin');

            return redirect('signin')->with('success', 'Password changed succesfully');
        } else {
            return back()->with('error', 'Confirm password does not match');
        }
    }

    public function sign_out(Request $request)
    {
        Session::flush();
        Auth::logout();

        return redirect('/signin');
    }
}
