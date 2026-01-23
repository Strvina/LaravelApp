@extends('layouts.nav-layout')

@section('content')
<div class="max-w-3xl mx-auto p-4">

    <h1 class="text-2xl font-bold mb-6">Contact Us</h1>

    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('contact.send') }}" method="POST" class="bg-white shadow-md rounded px-6 py-4 mb-6">
        @csrf

        <div class="mb-4">
            <label for="name" class="block font-semibold text-gray-700 mb-1">Name</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                   class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 @error('name') border-red-500 @enderror">
            @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="email" class="block font-semibold text-gray-700 mb-1">Email</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required
                   class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 @error('email') border-red-500 @enderror">
            @error('email') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
            <label for="message" class="block font-semibold text-gray-700 mb-1">Message</label>
            <textarea name="message" id="message" rows="5" required
                      class="w-full border px-3 py-2 rounded focus:outline-none focus:ring-2 focus:ring-blue-400 @error('message') border-red-500 @enderror">{{ old('message') }}</textarea>
            @error('message') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
        </div>

        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-semibold px-4 py-2 rounded transition-colors">
            Send Message
        </button>
    </form>

</div>
@endsection
