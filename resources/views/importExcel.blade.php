<!DOCTYPE html>
<html>
<head>
    <title>Laravel 5.7 Import Export Excel to database Example - ItSolutionStuff.com</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
</head>
<body>
   
<div class="container">
    <div class="card bg-light mt-3">
        <div class="card-header">
            Laravel 5.7 Import Export Excel to database Example - ItSolutionStuff.com
        </div>
        <div class="card-body">
            <form action="{{ route('importExcel') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" class="form-control">
                <br>
                <button class="btn btn-success">Import User Data</button>
                <a class="btn btn-warning" href="export">Export User Data</a>
            </form>
        </div>
    </div>
    
</div>
   
</body>
</html>