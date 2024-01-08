
const SelectBulletin = {

    firstTrademark: null,
    getSelectedBulletin: null,
    trademarks: [],
    bultenler: [],
    version:'1.0.0',
    // kullanıcının tüm markalarını getir 
    getAllBrand: function() {
        $.ajax({
            url: '/brand/yda-and-yim-get-all-trademark',
            method: 'GET',
            data:{},
            success: function (response) {
                if(response?.success == true) {
                    SelectBulletin.firstTrademark = response.data[0].url; 
                    // http://localhost:8080/company/observation/8/ar%C3%A7eliklunaris/12842658?type=1
                    // http://localhost:8080/company/observation/8/aria/13001393?type=1&bultenler=430,431,432,433&multiple=1
                    // http://localhost:8080/company/observation/7-11/EnergySpin/17775179?type=1&bultenler=430,431,432,433&multiple=1
                    let data = response.data.map((item) => {
                        return {
                            ...item,
                            url: item.url + `&bultenler=${SelectBulletin.bultenler.join(',')}&multiple=1`,
                        }
                    })
                
                    // $('#get-all-trademark-count').text(response.data.length);
                    localStorage.setItem("trademarkData", JSON.stringify(data));
                     window.location.href = `${SelectBulletin.firstTrademark}&bultenler=${SelectBulletin.bultenler.join(',')}&multiple=1`;

                } else {
                    console.log("Marka verileri getirilirken bir hata oluştu");
                }

            },
            error: function (err) {
                console.log(err);
            }
        });
    },

    goToTrademarks: function() {
        this.getAllBrand();
    },

    init: function () {
        $('.yda-and-yim-select2-multiple').select2({
            placeholder: 'Bülten Seçiniz',
            allowClear: false,
            multiple: true,
        })
    }

}


$(document).ready(function () {
    SelectBulletin.init()

    $('.bulten-checkbox').on('change', function() {
      
        if($(this).is(':checked')) {
            SelectBulletin.bultenler.push($(this).val());
        } else {
            SelectBulletin.bultenler = SelectBulletin.bultenler.filter((item) => item != $(this).val());
        }

        if(SelectBulletin.bultenler.length > 0) {
            $('#bulten-search-multiple').prop('disabled', false);
        } else {
            $('#bulten-search-multiple').prop('disabled', true);
        }
    })
    


    $('.aktif-bultenler-checkbox').on('change', function() {

        if($(this).is(':checked')) {
            // hepsini bul
            $('.bulten-checkbox').each(function() {
                // value değeri 430 eşit olanı check et
                if(scriptData.secilebilir_elemanlar.includes(Number($(this).val()))) {
                    $(this).prop('checked', true);
                    SelectBulletin.bultenler.push($(this).val());
                    $('#bulten-search-multiple').prop('disabled', false);
                } else {
                    $(this).prop('checked', false);
                }
            })
       
        } else {
            $('.bulten-checkbox').prop('checked', false);
            SelectBulletin.bultenler = [];
            $('#bulten-search-multiple').prop('disabled', true);
        }
    })



      

})