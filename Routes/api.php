<?php

use Illuminate\Http\Request;

Route::get('autheduser', 'CognitoAuthController@authUser')->middleware('auth:api');
