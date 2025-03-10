<div id="viewAlphabetCountsModal" class="modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="viewAlphabetCountsForm">
                <div class="modal-header">
                    <h3 class="modal-title" id="exampleModalLabel">View Alphabet Book Counts</h3>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label style="font-weight: bold;" for="userFullName" class="col-sm-1 col-form-label">User: </label>
                        <div class="col-sm-8">
                            <input id="userFullName" class="form-control-plaintext" readonly>
                        </div>
                    </div>
                    <div>
                        <table id="alphabetCountsTable" class="styledTable">
                            <thead>
                                <tr class="header">
                                    <th>Letter</th>
                                    <th>Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Populated with AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
