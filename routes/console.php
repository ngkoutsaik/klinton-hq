<?php

use App\Jobs\DeleteSentContactForms;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new DeleteSentContactForms)->daily();
