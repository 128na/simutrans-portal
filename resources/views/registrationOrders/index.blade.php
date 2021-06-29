@extends('layouts.front')

@section('title', '登録申請一覧')

@section('content')
    <h2>登録申請一覧</h2>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Twitter</th>
                    <th>Code</th>
                    <th>Request Info</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr class="{{ $item->status_color }}">
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->status }}</td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->email }}</td>
                        <td>{{ $item->twitter }}</td>
                        <td>{{ $item->code }}</td>
                        <td>{{ $item->request_info }}</td>
                        <td>{{ $item->updated_at->toDatetimeString() }}</td>
                        <td>
                            <button type="submit" form="form-applove"
                                formaction="{{ route('registrationOrders.update', $item) }}"
                                class="btn btn-success mb-3">APPROVE</button><br>
                            <button type="submit" form="form-reject"
                                formaction="{{ route('registrationOrders.update', $item) }}"
                                class="btn btn-danger">REJECT</button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <form id="form-applove" method="POST">
        @csrf
        <input type="hidden" name="status" value="approval">
    </form>
    <form id="form-reject" method="POST">
        @csrf
        <input type="hidden" name="status" value="rejected">
    </form>
@endsection
