
    @extends('layouts.app')
    @section('title')
        BNU CMS
    @stop

    @section('content')
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Survey Modal -->
    <div class="modal fade" id="surveyModal" tabindex="-1" role="dialog" aria-labelledby="surveyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; border-radius: 8px 8px 0 0;">
                    <h5 class="modal-title" id="surveyModalLabel" style="color: #202124;">
                        <i class="fa fa-clipboard-list" style="color: #4285f4;"></i> Library Survey
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color: #5f6368;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" style="padding: 2rem;">
                    <i class="fa fa-question-circle fa-3x mb-3" style="color: #4285f4;"></i>
                    <h4 class="mb-3" style="color: #202124; font-weight: 500;">We kindly invite you to participate in our Survey</h4>
                    <p class="text-muted" style="color: #5f6368;">Your feedback is important and will help us enhance the services.</p>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa; border-top: 1px solid #e9ecef; border-radius: 0 0 8px 8px;">
                    <button type="button" class="btn btn-light" id="closeModal" style="color: #5f6368; border: 1px solid #dadce0; padding: 8px 16px; border-radius: 4px;">
                        <i class="fa fa-times mr-2"></i>Close
                    </button>
                    <button type="button" class="btn" id="processSurvey" style="background-color: #4285f4; color: white; padding: 8px 16px; border-radius: 4px; border: none;">
                        <i class="fa fa-check mr-2"></i>Proceed
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-content {
            border: none;
        }
        .btn:hover {
            opacity: 0.9;
            transition: opacity 0.2s;
        }
        #processSurvey:hover {
            background-color: #3367d6;
        }
        #closeModal:hover {
            background-color: #f1f3f4;
        }
    </style>

    <!-- jQuery and Bootstrap JS -->
    <script>
    $(document).ready(function() {
        // Show modal on page load
        $('#surveyModal').modal('show');
        // Handle Process button click
        $('#processSurvey').click(function() {
            window.location.href = "{{ route('library-survey') }}";
        });
        // Handle Close button click
        $('#closeModal').click(function() {
            // Set session flag to prevent inclusion
            $.ajax({
                url: "{{ route('set-survey-flag') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    flag: 'closed'
                },
                success: function() {
                    $('#surveyModal').modal('hide');
                    window.location.reload();
                }
            });
        });
        // Handle modal close via X button
        $('.close').click(function() {
            // Set session flag to prevent inclusion
            $.ajax({
                url: "{{ route('set-survey-flag') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    flag: 'closed'
                },
                success: function() {
                    $('#surveyModal').modal('hide');
                    window.location.reload();
                }
            });
        });
        // Prevent modal from closing when clicking outside
        $('#surveyModal').modal({
            backdrop: 'static',
            keyboard: false
        });
    });
    </script>
    @endsection 

    

