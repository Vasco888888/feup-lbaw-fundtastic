@if(session('success'))
    <div style="padding:10px;background:#e6ffed;border:1px solid #c6f3d0;margin-bottom:12px">{{ session('success') }}</div>
@endif

@if(session('error'))
    <div style="padding:10px;background:#ffe6e6;border:1px solid #f3c6c6;margin-bottom:12px">{{ session('error') }}</div>
@endif

@if($errors->any())
    <div style="padding:10px;background:#fff4e6;border:1px solid #f3e0c6;margin-bottom:12px">
        <strong>There were some problems with your input:</strong>
        <ul style="margin-top:0.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
