@extends('layouts.master')

@section('pageTitle', $pageTitle)

@section('main')
<div class="form-container">
  <h1 class="form-title">{{ $pageTitle }}</h1>
  <form class="form" method="POST" action="{{ route('tasks.store') }}">
    @csrf 
  <div class="form-item">
      <label>Name:</label>
      <input class="form-input" type="text" value="" name="name">
      @error('name')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-item">
      <label>Detail:</label>
      <textarea class="form-text-area" name="detail"></textarea>
      @error('detail')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-item">
      <label>Due Date:</label>
      <input class="form-input" type="date" value="" name="due_date" >
      @error('due_date')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-item">
      <label>Progress:</label>
      <select class="form-input" name="status">
        <option value="not_started">Not Started</option>
        <option value="in_progress">In Progress</option>
        <option value="in_review">Waiting/In Review</option>
        <option value="completed">Completed</option>
      </select>
      @error('status')
          <div class="alert-danger">{{ $message }}</div>
        @enderror
    </div>
    <button type="submit" class="form-button">Submit</button>
  </form>
</div>
@endsection