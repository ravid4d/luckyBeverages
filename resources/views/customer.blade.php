<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <!-- Bootstrap 4 CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4 text-center">Customer Management</h2>

        <!-- Search Bar -->
        <form class="form-inline mb-3" method="GET" action="">
            <input 
                type="search" 
                name="search" 
                class="form-control mr-sm-2" 
                placeholder="Search by Name or Email" 
                {{-- value="{{ request('search') }}"  --}}
                aria-label="Search">
            <button class="btn btn-outline-primary" type="submit">Search</button>
        </form>

        <!-- Customer Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th>GivenName</th>
                        <th>FamilyName</th>
                        <th>FullyQualifiedName</th>
                        <th>CompanyName</th>
                        <th>DisplayName</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customer as $customers)
                        <tr>
                            {{-- <td>{{ $loop->iteration }}</td> --}}
                            <td>{{ $customers->GivenName }}</td>
                            <td>{{ $customers->FamilyName }}</td>
                            <td>{{ $customers->FullyQualifiedName }}</td>
                            <td>{{ $customers->CompanyName }}</td>
                            <td>{{ $customers->DisplayName}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No customers found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{-- {{ $customers->links() }} --}}
        </div>
    </div>

    <!-- Bootstrap 4 JS, Popper.js, and jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
