@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<section style="margin:2rem;">
    <h2>Reports</h2>

    @if($reports->isEmpty())
        <p class="muted">No reports found.</p>
    @else
        <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #eee;">
                    <th>#</th>
                    <th>Date</th>
                    <th>User</th>
                    <th>Comment</th>
                    <th>Campaign</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reports as $r)
                <tr style="border-bottom:1px solid #fafafa;">
                    <td>{{ $r->report_id }}</td>
                    <td>{{ $r->date }}</td>
                    <td>{{ $r->user_id }}</td>
                    <td>{{ $r->comment_id }}</td>
                    <td>{{ $r->campaign_id ?? '—' }}</td>
                    <td style="max-width:40ch;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $r->reason }}</td>
                    <td>{{ $r->status ?? 'open' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</section>
@endsection
