let ApplicationFile = {
    trademark_id: null,

    dataTable: function (tableId) {
        let account_ref = scriptData.session_account_ref;
        $(tableId).DataTable({
            destroy: true, paging: true, ordering: true, autoWidth: false, //  page length
            pageLength: 10, dataSrc: "data", responsive: true,

            pagingType: "simple_numbers", searching: true,


            ajax: {
                url: `/brand/domestic/detail-modal?type=invoice&col_id=${account_ref}`, type: "GET", beforeSend: function (xhr) {
                    //   $(".main-preloader").removeClass("d-none");
                },

                success: function (data) {
                    console.log(data);
                },

                error: function (xhr, error, code) {
                    console.log(xhr);
                },
            },

            drawCallback: function (settings) {
                // $(".main-preloader").addClass("d-none");
                console.log(settings);
            },

            // columnDefs: [{
            //     targets: 0, // center
            //     className: "text-center", // style: "width: 76px;",
            //     render: function (data, type, row, meta) {
            //         return `
            // <div style="text-align: left; width: 76px;"><img class="v-image v-widget" src="http://apiz.ankarapatent.com/fileServer/tmImage?res=64&amp;appNo=${row.col_application_number}" alt=""></div>
            // `;
            //     },
            // }, {
            //     targets: 1, render: function (data, type, row, meta) {
            //         return `
            // <span class="text-primary text-decoration-underline " onclick="ApplicationFile.getTrademarkInfo(${row.col_id})"
            // data-bs-toggle="modal" data-bs-target=".detay-modal">
			// 		<span>${row.col_trademark}</span>
			// 	</span>
            // `;
            //     },
            // }, {
            //     targets: 2, render: function (data, type, row, meta) {
            //         return `
            // ${row.col_class_string ? row.col_class_string : "-"}
            // `;
            //     },
            // },
            //
            //     {
            //         targets: 3, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_file_number ?? "-"}
            // `;
            //         },
            //     }, {
            //         targets: 4, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_application_number ?? "-"}
            // `;
            //         },
            //     }, {
            //         targets: 5, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_registration_date ?? "-"}
            // `;
            //         },
            //     }, {
            //         targets: 6, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_registration_number ?? '-'}
            // `;
            //         },
            //     }, {
            //         targets: 7, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_renewal_date ?? "-"}
            // `;
            //         },
            //     }, {
            //         targets: 8, render: function (data, type, row, meta) {
            //             return `
            // ${row.col_last_status ?? "-"}
            // `;
            //         },
            //     }, {
            //         targets: 9,
            //
            //         render: function (data, type, row, meta) {
            //
            //
            //             let siniflar = row.col_class_string.replaceAll(",", "-");
            //
            //             siniflar = siniflar.split("-");
            //
            //
            //             siniflar = siniflar.map((item) => {
            //                 return parseInt(item)
            //             }).join("-")
            //
            //
            //             console.log(siniflar);
            //
            //             let url = `/company/observation/${siniflar}/${row.col_trademark}/${row.col_id}`.replaceAll(" ", "");
            //             return ` <a href="${url}" class="text-primary text-decoration-underline">Gözlemi Başlat</a>
            // `;
            //         },
            //     },],

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
