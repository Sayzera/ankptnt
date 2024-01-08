let ApplicationFile = {
    trademark_id: null,

    dataTable: function (tableId) {
        $(''+tableId+' thead tr')
        .clone(true)
        .addClass('filters')
        .appendTo(''+tableId+' thead');

        let account_ref = scriptData.session_account_ref;
        $(tableId).DataTable({
            initComplete: function () {
                var api = this.api();
     
                // For each column
                api
                    .columns()
                    .eq(0)
                    .each(function (colIdx) {
                        // Set the header cell to contain the input element
                        var cell = $('.filters th').eq(
                            $(api.column(colIdx).header()).index()
                        );
                        var title = $(cell).text();
                        $(cell).html('<input type="text" placeholder="' + title + '" />');
     
                        // On every keypress in this input
                        $(
                            'input',
                            $('.filters th').eq($(api.column(colIdx).header()).index())
                        )
                            .off('keyup change')
                            .on('change', function (e) {
                                // Get the search value
                                $(this).attr('title', $(this).val());
                                var regexr = '({search})'; //$(this).parents('th').find('select').val();
     
                                var cursorPosition = this.selectionStart;
                                // Search the column for that value
                                api
                                    .column(colIdx)
                                    .search(
                                        this.value != ''
                                            ? regexr.replace('{search}', '(((' + this.value + ')))')
                                            : '',
                                        this.value != '',
                                        this.value == ''
                                    )
                                    .draw();
                            })
                            .on('keyup', function (e) {
                                e.stopPropagation();
     
                                $(this).trigger('change');
                                $(this)
                                    .focus()[0]
                                    .setSelectionRange(cursorPosition, cursorPosition);
                            });
                    });
            },
            destroy: true, paging: true, ordering: true, autoWidth: false, //  page length
            pageLength: 10, dataSrc: "data", responsive: true,

            pagingType: "simple_numbers", searching: true,


            ajax: {
                url: `/brand/domestic/detail-modal?type=invoice&col_id=${account_ref}`, type: "GET", beforeSend: function (xhr) {
                    //   $(".main-preloader").removeClass("d-none");
                },

                error: function (xhr, error, code) {
                    console.log(xhr);
                },
            },

            drawCallback: function (settings) {
                // $(".main-preloader").addClass("d-none");
                console.log(settings);
            },

            columnDefs: [
            {
                targets: 0, render: function (data, type, row, meta) {
                    return `
                ${row.col_ettn }
            `;
                },
            }, {
                targets: 1, render: function (data, type, row, meta) {
                    return `
               ${row.col_lks_date ? row.col_lks_date : "-"}
            `;
                },
            },

                {
                    targets: 2, render: function (data, type, row, meta) {
                        return `
            ${row.col_total_tl_cost  ?? "-"} 
            `;
                    },
                }, {
                    targets: 3, render: function (data, type, row, meta) {
                        return `
            ${row.col_comment ?? "-"}
            `;
                    },
                }, {
                    targets: 4, render: function (data, type, row, meta) {
                        return `
          ${row.col_status}
            `;
                    }
                },
                {
                    targets: 5, render: function (data, type, row, meta) {
                        return `
                        <a href="#" target="_blank" class="text-primary text-decoration-underline">
                        Detay
</a>
            `;
                    },
                },
            ],
            order: [
                [1, "desc"]
            ],

            fixedHeader: false, language: {
                url: scriptData.dataTableJsonLang,
                processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i>Processing...',
                paginate: {
                    next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                    previous: '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
                },
            },

        });
    },

    init: function () {
this.dataTable('#applicationFileList')
    }, run: function () {

        this.init();
    },
};

$(document).ready(function () {
    ApplicationFile.run();
});

$('input[class="basvuru-tarihi"]').daterangepicker({
    // onchange
    autoUpdateInput: false,
});
