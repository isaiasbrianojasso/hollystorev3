<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rol;
use App\Models\Plan;

class Usuario extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = new User();
        $user->name= 'HollyDev';
        $user->email='gomezlopeznapoleon@gmail.com';
        $user->password = bcrypt('12345678');
        $user->rol_id=1;
        $user->plan_id=3;
        $user->creditos=100;
        $user->fechaactivo='2022-06-30 00:00:00';
        $user->fechafinal='2024-06-30 00:00:00';
        $user->estatus='1';
        $user->telefono='122323';
        $user->chatid=142398483;
        //$user->avatar="https://i.kym-cdn.com/photos/images/newsfeed/001/658/936/411.jpg";
        $user->profile_photo_path="https://i.kym-cdn.com/photos/images/newsfeed/001/658/936/411.jpg";
        $user->save();

        $plan= new Plan();
        $plan->nombre="Basic";
        $plan->tipo="basic";
        $plan->descripcion="The most common Plan";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Standard";
        $plan->tipo="standard";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Premiun";
        $plan->tipo="premiun";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();



        $plan= new Plan();
        $plan->nombre="Rent 2 years plan";
        $plan->tipo="rent1y";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();


        $plan= new Plan();
        $plan->nombre="Rent 1 year plan";
        $plan->tipo="rent1y";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();


        $plan= new Plan();
        $plan->nombre="Rent 1 week plan";
        $plan->tipo="rent1w";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Rent 1 month plan";
        $plan->tipo="rent1m";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Rent 3 month plan";
        $plan->tipo="rent3m";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Rent 6 month plan";
        $plan->tipo="rent6m";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Rent 7 days";
        $plan->tipo="rent7days";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();

        $plan= new Plan();
        $plan->nombre="Rent 15 days";
        $plan->tipo="rent15days";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();




        $plan= new Plan();
        $plan->nombre="Demo";
        $plan->tipo="demo";
        $plan->descripcion="the best plan for most user include some services extra ";
        $plan->estatus=1;
        $plan->save();


        $plan= new Rol();
        $plan->nombre="Admin";
        $plan->tipo="admin";
        $plan->estatus=1;
        $plan->save();

        $plan= new Rol();
        $plan->nombre="Customer";
        $plan->tipo="customer";
        $plan->estatus=1;
        $plan->save();

        $plan= new Rol();
        $plan->nombre="Reseller";
        $plan->tipo="reseller";
        $plan->estatus=1;
        $plan->save();
    }
}
