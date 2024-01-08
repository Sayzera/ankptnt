let observation = {
  observationApizList: [],
  selectedBulletinId: parseInt($("#brand-select").val()),
  tableHeadCopy: null,
  observationTable: function () {
    var table = $("#example5").DataTable({
      destroy: true,
      searching: true,
      paging: true,
      select: true,
      pageLength: 100,
      info: false,
      lengthChange: false,
      language: {
        // url: twigData.dataTableLangUrl,
        paginate: {
          next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
          previous:
            '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
        },
      },

      initComplete: function () {
        var api = this.api();

        // For each column
        api
          .columns()
          .eq(0)
          .each(function (colIdx) {
            // Set the header cell to contain the input element
            var cell = $(".filters th").eq(
              $(api.column(colIdx).header()).index()
            );
            var title = $(cell).text();

            if (colIdx !== 0 && colIdx !== 7) {
              $(cell).html('<input type="text" placeholder="' + title + '" />');
            } else {
              $(cell).html(
                '<input type="text" placeholder="' + title + '" disabled />'
              );
            }

            // On every keypress in this input
            $(
              "input",
              $(".filters th").eq($(api.column(colIdx).header()).index())
            )
              .off("keyup change")
              .on("change", function (e) {
                // Get the search value
                $(this).attr("title", $(this).val());
                var regexr = "({search})"; //$(this).parents('th').find('select').val();

                var cursorPosition = this.selectionStart;
                // Search the column for that value
                api
                  .column(colIdx)
                  .search(
                    this.value != ""
                      ? regexr.replace("{search}", "(((" + this.value + ")))")
                      : "",
                    this.value != "",
                    this.value == ""
                  )
                  .draw();
              })
              .on("keyup", function (e) {
                e.stopPropagation();

                $(this).trigger("change");
                $(this)
                  .focus()[0]
                  .setSelectionRange(cursorPosition, cursorPosition);
              });
          });
      },

      columnDefs: [
        {
          orderable: false,
          targets: "no-sort",
        },
      ],
      // desc
    });

    return table;
  },

  ScrollGoTo: function (className) {
    var top = $(className).offset().top - 100;
    $("html, body").animate(
      {
        scrollTop: top,
      },
      1000
    );
  },

  clickToHamburger: function (className) {
    // eğer is-active clası yoksa
    if (!$(className).hasClass("is-active")) {
      $(className).trigger("click");
    }
  },

  ClickStartObservation: function () {
    let bultenNo = localStorage.getItem("bultenNo");

    // selected option make selected

    if (bultenNo) {
      $("#brand-select").val(bultenNo).trigger("change");
    }

    $('button[name="start-observation"]').trigger("click");
  },

  SelectTradeMarkInput: function () {
    let marka = $('input[name="marka-adi"]');
    // read only
    marka.val(twigData.marka);
  },

  SelectClassInput: function () {
    $("#class-select")
      .select2({
        placeholder: "Sınıf Seç",
        // data ekle birden fazla
        allowClear: true,
        disabled: true,
      })
      .attr("readonly", true);

    $("#class-select").val(twigData.sinif.split("-")).trigger("change");
  },

  observationResultCount: function (className, count) {
    $(className).html(count + " adet sonuç bulundu.");
  },
  getObservationListFormData: function (e) {
    // get form values
    var formValues = $(e.target).serializeArray();

    let siniflar = [];
    // formValues.forEach((element) => {
    //   if (element.name == "sinif") {
    //     siniflar.push(element.value);
    //     // siniflar.push($('select[name="sinif"]').val());
    //   }
    // });

    siniflar.push($('select[name="sinif"]').val());

    formValues = formValues.filter(function (item) {
      return item.name != "sinif";
    });

    formValues.push({
      name: "bulten-no",
      value: $('select[name="bulten-no"]').val(),
    });

    // trademark_id
    formValues.push({
      name: "trademark_id",
      value: twigData.id,
    });
    formValues.push({ name: "arrBulletinNo", value: twigData.arrBulletin });

    formValues.push({ name: "siniflar", value: siniflar });
    formValues.push({
      name: "marka-adi",
      value: $('input[name="marka-adi"]').val(),
    });
    let formData = new FormData();
    formValues.forEach((element) => {
      formData.append(element.name, element.value);
    });

    formData.append("token", twigData.token);

    return formData;
  },
  

  sendObservationListRequestWithAjax: function (formValues) {


    $.ajax({
      type: "POST",
      url: twigData.getObservationListUrl,
      data: formValues,
      processData: false,
      contentType: false,
      beforeSend: function () {
        $(".observation-search").removeClass("d-none");
      },
      success: function (response) {
        // =
        // selectedBulletinId >= lastBulletinId &&
        // selectedBulletinId <= endBulletinNo ||

        $(".form-errors").addClass("d-none");
        $(".observation-search").addClass("d-none");
        $(".gozlem-container-preloader").addClass("d-none");
        $(".gozlem-container").removeClass("d-none");

        // observation.ScrollGoTo(".marka-gozlem-list");
        observation.clickToHamburger(".hamburger");
        observation.observationResultCount(
          ".observation-brand-result",
          response?.data?.trademarkSearchList?.length
        );
        if (response?.data?.trademarkSearchList?.length > 0) {
          let list = response.data.trademarkSearchList;

          if(observation.tableHeadCopy == null) {
            $("#example5 thead tr")
            .clone(true)
            .addClass("filters")
            .appendTo("#example5 thead");
            
            observation.tableHeadCopy = true;
          }
      

       

          list.sort((a, b) => b.shapeSimilarity - a.shapeSimilarity);

          observation.observationApizList = list;


          $('#example5').DataTable({
            initComplete: function () {
              var api = this.api();
      
              // For each column
              api
                .columns()
                .eq(0)
                .each(function (colIdx) {
                  // Set the header cell to contain the input element
                  var cell = $(".filters th").eq(
                    $(api.column(colIdx).header()).index()
                  );
                  var title = $(cell).text();
      
                  if (colIdx !== 0 && colIdx !== 7) {
                    $(cell).html('<input type="text" placeholder="' + title + '" />');
                  } else {
                    $(cell).html(
                      '<input type="text" placeholder="' + title + '" disabled />'
                    );
                  }
      
                  // On every keypress in this input
                  $(
                    "input",
                    $(".filters th").eq($(api.column(colIdx).header()).index())
                  )
                    .off("keyup change")
                    .on("change", function (e) {
                      // Get the search value
                      $(this).attr("title", $(this).val());
                      var regexr = "({search})"; //$(this).parents('th').find('select').val();
      
                      var cursorPosition = this.selectionStart;
                      // Search the column for that value
                      api
                        .column(colIdx)
                        .search(
                          this.value != ""
                            ? regexr.replace("{search}", "(((" + this.value + ")))")
                            : "",
                          this.value != "",
                          this.value == ""
                        )
                        .draw();
                    })
                    .on("keyup", function (e) {
                      e.stopPropagation();
      
                      $(this).trigger("change");
                      $(this)
                        .focus()[0]
                        .setSelectionRange(cursorPosition, cursorPosition);
                    });
                });
            },
            columnDefs: [
              {
                orderable: false,
                targets: "no-sort",
              },
            ],
            destroy: true,
            searching: true,
            paging: true,
            ordering:false,
           
            select: true,
            pageLength: 100,

            info: false,
            lengthChange: false,
            language: {
              url: twigData.dataTableLangUrl,
              paginate: {
                next: '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
                previous:
                  '<i class="fa fa-angle-double-left" aria-hidden="true"></i>',
              },
            },
            data: list,
            columnDefs: [
              {
                targets: 0, // center
                render: function (data, type, row, meta) {
                  return `
                  <td>
                  <div class="form-check custom-checkbox ms-2">
                  <div style="text-align: left; width: 76px;"><img class="v-image v-widget" loading="lazy" src="http://apiz.ankarapatent.com/fileServer/tmImage?res=64&amp;appNo=${row.applicationNo}" alt=""></div>           
                  </div>
              </td>
                  `;
                },
              },
              {
                targets: 1, // center
                render: function (data, type, row, meta) {
                  return `
                  <a href="#" >
                  ${row.renklendirilmisMarka}
                  </a>
                  `;
                },
              },
              {
                targets: 2, // center
                render: function (data, type, row, meta) {
                  return `
                  ${row.holderName}
                  `;
                },
              },
              {
                targets: 3, // center
                render: function (data, type, row, meta) {
                  return `
                  <div style="width:100% !important">
                  <p style="width:100%">
                  ${row.siniflar}</p>

              
                  <span class="text-primary text-decoration-underline c-pointer"
                  data-bs-toggle="modal"
                  data-bs-target=".envanter-listesi"
                  onclick="observation.getMarkaEsyaListesi('${row.applicationNo}', ${meta.row})"
                  ">Eşya listesi</span>
                </div>
                  `;
                },
              },
              {
                targets: 4, // center
                render: function (data, type, row, meta) {
                  return `
                  ${row.applicationNo}
                  `;
                },
              },
              {
                targets: 5, // center
                render: function (data, type, row, meta) {
                  return `
                <span> ${row.benzerlikOrani}</span>
                  `;
                },
              },
              {
                targets: 6, // center
                render: function (data, type, row, meta) {
          
                  let bultenTarihi = row.bultenTarihi;

                  return `
                  <div style="    width: 100% !important;
                  text-align: center;">
                 <span style="font-weight:bold">
                 ${row.bulletinNo}
                 </span>  
                 <span class="d-flex flex-column" >
                 ${
                   bultenTarihi?.length > 0
                     ? "Son itiraz tarihi:" + bultenTarihi[0].bulten_tarihi_durumu
                     : ""
                 }
                 </span>
  
                 ${
                   row.nerde_kaldim != null
                     ? `
                  <span class='badge badge-rounded badge-warning' style="font-weight:bold; font-size:13px">Gözlem burada kaldı</span>
                  `
                     : ""
                 }
                  </div>
                  `;
                },
              },
              {
                targets: 7, // center
                render: function (data, type, row, meta) {
                  let isOkeyForObservation = twigData.selectable_elements.includes(
                    row.bulletinNo
                  );
                  return `
                  <div  class="d-flex justify-content-between ana-div-icon" style="width:100% !important"> 
                  <div style="width:100% !important">
                  ${
                    row.created_at == null
                      ? isOkeyForObservation
                        ? `<span  class="btn btn-primary btn-sm" onclick="observation.disapprovalAlert(${meta.row}, this)">İtiraz Et </span>`
                        : "<span class='btn btn-danger btn-sm'>İtiraz Edilemez</span>"
                      : `<span onclick="observation.itirazEdildiInfo()" class="">
                        <strong>${row.created_at} tarihinde itiraz talimatınız iletildi.</strong>
                      </span>`
                  }</div>
    
                  <div class="alt-icon-info text-primary" style="text-decoration:underline; cursor:pointer; width:100% !important"  onclick="observation.nerde_kaldim(${meta.row}, this)">
                   ${
                    row.nerde_kaldim != null
                       ? `İşareti Kaldır`
                       : `Gözlem burda kaldı`
                   }
                  </div>
                   </div>
                  `;
                },
              },

            ]
          })

      
        } else {
          observation.observationTable().clear().draw();
        }
      },
      error: function (xhr) {
        $(".observation-search").addClass("d-none");

        let error = xhr.responseJSON;

        if (error?.validations) {
          let errorTemp = "";
          Object.keys(error?.validations).map((key, index) => {
            errorTemp += `<p class="text-danger mt-1" >${error?.validations[key]}</p>`;
          });

          $(".form-errors").removeClass("d-none").html(errorTemp);
        }

        commonjs.toastrError("Formda hata var. Lütfen kontrol ediniz.");
      },
    });
  },
  getObservationListOnSubmit: function (e) {
    e.preventDefault();
    let formValues = this.getObservationListFormData(e);
    this.sendObservationListRequestWithAjax(formValues);
  },

  init: function () {
    this.SelectClassInput();
    this.SelectTradeMarkInput();
    this.ClickStartObservation();
    this.sonrakiMarkaDetay();
    this.oncekiMarkaDetay();
  },

  benzerHarfleriRenklendir(similarKeyword, mainKeyword) {
    let mainKeywordArr = mainKeyword.split(""); // meb deneme serisi -> smilerKeyword
    let similarKeywordArr = similarKeyword.split(""); // deneme2
    let temp = [];
    let count = 0;
    // Benzerliği aranan kelimeyi dolaş
    for (let i = 0; i < mainKeywordArr.length; i++) {
      let matchFound = false;

      for (let j = 0; j < similarKeywordArr.length; j++) {
        if (
          mainKeywordArr[i] === similarKeywordArr[j] &&
          mainKeywordArr[i + 1] === similarKeywordArr[j + 1]
        ) {
          matchFound = true;

          // Ardışık benzer 4 harfi kontrol et
          let k = i;
          let l = j;

          while (
            k < mainKeywordArr.length &&
            l < similarKeywordArr.length &&
            mainKeywordArr[k] === similarKeywordArr[l]
          ) {
            temp.push(
              '<span style="color: red;">' + mainKeywordArr[k] + "</span>"
            );
            k++;
            l++;
            count++;
          }

          // Ardışık 4 harfi bulduğumuzda, döngüde devam etmesini sağlamak için i'yi güncelle
          i = k - 1;

          break;
        } else {
          // count kadar olan kelimeleri ekle

          count = 0;
        }
      }

      if (!matchFound) {
        temp.push(mainKeywordArr[i]);
      }
    }

    return temp.join("");
  },

  itirazEdildiInfo: function () {
    swal.fire({
      title: "Bilgi",
      html: `İtiraz başvurusu yapıldı.`,
      type: "success",
      showCancelButton: false,
      confirmButtonText: "Tamam",
    });
  },

  sonrakiMarkaDetay: function () {
    let id = twigData.id;

    let trademarkList = localStorage.getItem("trademarkData")
      ? JSON.parse(localStorage.getItem("trademarkData"))
      : [];

    let index = trademarkList.findIndex((item) => item.id == id);

    let sonrakiMarka = trademarkList[index + 1];

    if (sonrakiMarka != undefined) {
      $(".sonraki-marka-text").removeClass("d-none");
      $(".sonraki-marka-text").html(
        `İleri >>  ${sonrakiMarka.trademark} (${sonrakiMarka.order}/${trademarkList.length}) `
      );
      $(".sonraki-marka-text").attr("href", sonrakiMarka.url);
    } else {
      $(".sonraki-marka-text").addClass("d-none");
    }

    // content
  },
  oncekiMarkaDetay: function () {
    let id = twigData.id;

    let trademarkList = localStorage.getItem("trademarkData")
      ? JSON.parse(localStorage.getItem("trademarkData"))
      : [];

    let index = trademarkList.findIndex((item) => item.id == id);

    if (index == 0) {
      $(".onceki-marka").addClass("d-none");
    }

    let oncekiMarka = trademarkList[index - 1];

    $(".onceki-marka-text").html(
      ` Geri <<  ${oncekiMarka.trademark} (${oncekiMarka.order}/${trademarkList.length})`
    );
    $(".onceki-marka-text").attr("href", oncekiMarka.url);
  },

  disapprovalAlert(index, e) {
    let bulletinNo = parseInt($("#brand-select").val());
    let selectedObservationItem = this.observationApizList[index];

    swal
      .fire({
        title: "İtiraz etmek istediğinize emin misiniz?",
        html: `<b style='color:red'>${bulletinNo}</b>. Resmi Marka Bülteninde yanınlanan <b style='color:red'>${selectedObservationItem.applicationNo}</b> sayılı <b style='color:red'>${selectedObservationItem.trademarkName}</b> ibareli benzer markaya ilişkin itiraz başvurusu yapılacaktır.<b> İtiraz ücretimiz KDV dahil 9940,78 TL dır</b>`,
        type: "warning",

        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "İtiraz Et",
        cancelButtonText: "Vazgeç",
      })
      .then((result) => {
        if (result?.value) {
          let id = twigData.id;

          let trademarkList = localStorage.getItem("trademarkData")
            ? JSON.parse(localStorage.getItem("trademarkData"))
            : [];

          let index = trademarkList.findIndex((item) => item.id == id);

          let marka = trademarkList[index];

          // swal.fire("Saved!", "", "success");
          $.ajax({
            url: "/company/observation/objection",
            type: "POST",
            data: {
              applicationNo: selectedObservationItem.applicationNo,
              bulletinNo: bulletinNo,
              classString: selectedObservationItem.niceClasses,
              trademark_id: marka.id,
              trademark: marka.trademark,
              itirazEdilenMarka: selectedObservationItem.trademarkName,
            },
            beforeSend: function () {
              $(".observation-search").removeClass("d-none");
            },

            success: function (response) {
              $(".observation-search").addClass("d-none");

              if (response.status == "success") {
                // disabled
                $(e).attr("disabled", true);
                $(e).attr("onclick", null).unbind("click");
                $(e).html("İtiraz Edildi");
                swal.fire("İtiraz başvurusu yapıldı", "", "success");
              } else {
                swal.fire("İtiraz başvurusu yapılamadı", "", "error");
              }
            },
            error: function (xhr) {
              console.log(xhr);
            },
          });
        }
      });
  },
  nerde_kaldim(index, e) {
    let bulletinNo = parseInt($("#brand-select").val());
    let selectedObservationItem = this.observationApizList[index];

    let id = twigData.id;

    let trademarkList = localStorage.getItem("trademarkData")
      ? JSON.parse(localStorage.getItem("trademarkData"))
      : [];

    let index_ = trademarkList.findIndex((item) => item.id == id);
    let marka = trademarkList[index_];

    // swal.fire("Saved!", "", "success");
    $.ajax({
      url: "/company/observation/nerder-kaldim",
      type: "POST",
      data: {
        applicationNo: selectedObservationItem.applicationNo,
        bulletinNo: bulletinNo,
        classString: selectedObservationItem.niceClasses,
        trademark_id: marka.id,
        trademark: marka.trademark,
        itirazEdilenMarka: selectedObservationItem.trademarkName,
      },
      beforeSend: function () {
        $(".observation-search").removeClass("d-none");
      },

      success: function (response) {
        $(".observation-search").addClass("d-none");

        if (response.status == "success") {
          // disabled
          // $(e).attr("disabled", true);
          // $(e).attr("onclick", null).unbind("click");
          $(e).html(response.message);
        } else {
          $(e).html(response.message);
        }
      },
    });
  },

  loading: function () {
    $(".main-preloader").removeClass("d-none");
    $(".main-preloader ").css("display", "flex");
  },

  endLoading: function () {
    $(".main-preloader").addClass("d-none");
    $(".main-preloader").css("display", "none");
  },

  /**
   * Gözlem sonucundan dönen markanın eşya listesi
   */
  getMarkaEsyaListesi: function (application_number, index) {
    // gözlem için tıklanan listenin o anki elemanı
    let listItem = observation.observationApizList[index];
    /**
     * Eşya listesi yapılacak firmanın marka bilgileri
     */

    $("#esya-listesi-gozlem-markasi").html(
      `${listItem.trademarkName} ${listItem.applicationNo}`
    );

    // Marka sahibi başvuru numarası
    let my_brand_the_application_number = _brand.basvuru_numarasi;

    $.ajax({
      url: "/company/observation/get-items",
      type: "POST",
      data: {
        applicationno: application_number,
        // Marka sahibi başvuru numarası
        basvuru_numarasi: my_brand_the_application_number,
      },
      beforeSend: function () {
        observation.loading();
      },
      success: function (response) {
        observation.endLoading();

        if (response.status == "success") {
          let data = response.data;

          $("#my-envanter-listesi-container").html(
            data.basvuru_sahibi_class_aciklama
          );
          $("#envanter-listesi-container").html(
            data.basvuru_itiraz_class_aciklama
          );

          // basvuru sahibi
          let esya_listesi_marka_numaralari = "";
          data.basvuru_sahibi_col_nice_classes?.map((item) => {
            // in array basvuru_itiraz_col_nice_classes
            let inArray = data.basvuru_itiraz_col_nice_classes?.includes(item);
            if (inArray) {
              esya_listesi_marka_numaralari += `<a href="javascript:void(0)" class="badge badge-danger mt-1">${item}</a>`;
            } else {
              esya_listesi_marka_numaralari += `<a href="javascript:void(0)" class="badge badge-dark mt-1">${item}</a>`;
            }
          });

          $("#esya-listesi-marka-numaralari").html(
            '<span class="font-bold" >Eşya Listesi</span> ' +
              esya_listesi_marka_numaralari
          );

          // itiraz edilecek kisi

          let esya_listesi_marka_numaralari_itiraz = "";
          data.basvuru_itiraz_col_nice_classes?.map((item) => {
            // in array basvuru_itiraz_col_nice_classes
            let inArray = data.basvuru_sahibi_col_nice_classes?.includes(item);
            if (inArray) {
              esya_listesi_marka_numaralari_itiraz += `<a href="javascript:void(0)" class="badge badge-danger mt-1">${item}</a>`;
            } else {
              esya_listesi_marka_numaralari_itiraz += `<a href="javascript:void(0)" class="badge badge-dark mt-1">${item}</a>`;
            }
          });

          $("#esya-listesi-marka-numaralari-itiraz").html(
            '<span class="font-bold" >Eşya Listesi</span> ' +
              esya_listesi_marka_numaralari_itiraz
          );
        }
      },
    });
  },

  run: function () {
    this.init();
  },
};

$(document).ready(function () {
  observation.run();
});
