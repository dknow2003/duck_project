@if($message = session('flash_message'))
  <!-- Toastr -->
  <script src="{{ asset('js/plugins/toastr/toastr.min.js') }}"></script>
  <script>
    $(document).ready(function () {

      setTimeout(function () {
        toastr.options = {
          closeButton: true,
          progressBar: true,
          showMethod: 'slideDown',
          timeOut: 4000
        };

        @if(is_array($message))
          @foreach($message as $single)
            @if(is_array($single))
               toastr.{{ $single[1] }}('{{ $single[0] }}');
            @else
               toastr.success('{{ $single }}');
            @endif
          @endforeach
        @else
           toastr.success('{{ $message }}');
        @endif

      }, 1300);
    });
  </script>
@endif
