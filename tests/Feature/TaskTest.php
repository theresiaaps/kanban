<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    private $mockedUsers=[];
    private $mockedTasks=[];


    public function setUp():void
    {
        parent::setUp();

        User::factory()->create();
        User::factory()->create();

        $user1=User::first();
        $user2=User::where('id','!=',  $user1->id)->first();

        array_push($this->mockedUsers, $user1, $user2);

        $this->actingAs($user1);

        $tasks= [
            [
            'name'=>'Task 1',
            'status'=> Task::STATUS_NOT_STARTED,
            'user_id'=> $user1->id,
            ],
            [
            'name'=>'Task 2',
            'status'=> Task::STATUS_IN_PROGRESS,
            'user_id'=> $user1->id,
            ],
            [
            'name'=>'Task 3',
            'status'=> Task::STATUS_COMPLETED,
            'user_id'=> $user1->id,
            ],
            [
            'name'=>'Task 4',
            'status'=> Task::STATUS_COMPLETED,
            'user_id'=> $user2->id,
            ],
    ];

    Task::insert($tasks);

    $this->mockedTasks = Task::with('user', 'files')
        ->get()
        ->toArray();
    }

    public function test_redirect_not_logged_in_user():void
    {
        Auth::logout();
        $response = $this->get(route('home'));
        $response->assertStatus(302);
    }

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    public function test_home():void
    {
        $response = $this->get(route('home'));
        $response->assertStatus(200);

        $response->assertViewIs('home');
        $response->assertViewHas('completed_count');
        $response->assertViewHas('uncompleted_count');

        $completed_count=$response->viewData('completed_count');
        $uncompleted_count=$response->viewData('uncompleted_count');

        $this->assertEquals(1, $completed_count);
        $this->assertEquals(2, $uncompleted_count);

    }
    public function task_index():void
    {
      
    }
}
