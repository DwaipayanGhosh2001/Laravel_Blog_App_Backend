<!doctype html>
<html lang="en">
    <head>
        <title>Title</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <!-- Bootstrap CSS v5.2.1 -->
        <link rel="stylesheet" href="./css/login.css">

        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body>
        <div>
            
        </div>
        <h2 class="mt-5 ms-5 login-section">Register.</h2>
        <div class="min-vh-100 d-flex justify-content-center mt-5">
            <div class="w-100" style="max-width: 600px">
                {{-- Form submit using post method --}}
                {{-- <form action="{{ url('/') }}/api/user/register" method="post">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            id="name"
                        />
                        <small id="name-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            id="email"
                        />
                        <small id="email-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            id="password"
                        />
                        <small id="password-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name="confirm_password"
                            id="confirm_password"
                        />
                        <small id="confirm-password-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mt-3">
                        <input type="submit" value="Submit" class="btn btn-primary rounded-2 "/>
                    </div>
                </form> --}}
                {{-- <form id="registrationForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input
                            type="text"
                            class="form-control"
                            name="name"
                            id="name"
                        />
                        <small id="name-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input
                            type="email"
                            class="form-control"
                            name="email"
                            id="email"
                        />
                        <small id="email-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name="password"
                            id="password"
                        />
                        <small id="password-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input
                            type="password"
                            class="form-control"
                            name="confirm_password"
                            id="confirm_password"
                        />
                        <small id="confirm-password-help" class="form-text text-muted">Help text</small>
                    </div>
                    <div class="mt-3">
                        <button type="button" id="submitBtn" class="btn btn-primary rounded-2">Submit</button>
                    </div>
                </form> --}}
            </div>
            
        </div>
        @yield('content')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('submitBtn').addEventListener('click', function() {
                    var formData = new FormData(document.getElementById('registrationForm'));
                    console.log(formData);
                    fetch("{{ url('/') }}/api/user/register", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        console.log(data);
                        // Handle response data
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Handle errors
                    });
                });
            });
            </script>
       
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
