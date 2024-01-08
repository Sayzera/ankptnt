let listenerArr = [];

function showCard(className, data, type) {
  className.map((item) => {
    let cardCount = parseInt(data[item.dataName]);

    if (cardCount > 0) {
      if (type === "patent") {
        $(".patent").text("Özet");
      } else if (type === "yurtici") {
        $(".yurtici").text("Özet");
      } else if (type === "yurtdisi") {
        $(".yurtdisi").text("Özet");
      } else if (type === "tasarim") {
        $(".tasarim").text("Özet");
      }
      $(`.${item.className}`).removeClass("d-none");
    }

    //event listener check
    if (listenerArr.indexOf(item.className) === -1) {
      $(`.${item.className}`).click(function () {

        if(apizDurum == true) {
          return false
        }

        if (type === "patent") {
          window.open("/patent/application-document?type=" + item.name, '_blank');
        } else if (type === "yurtici") {
          window.open("/brand/domestic/international-all?type=" + item.name, '_blank');
        } else if (type === "yurtdisi") {
          window.open("/brand/international?type=" + item.name, '_blank');
        } else if (type === "tasarim") {
          window.open("/design/design?type=" + item.name, '_blank');
        }
      });
      listenerArr.push(item.className);
    }
  });
}

function autoFillData() {
  $("#patent_toplam").html(data.patent_toplam);
  $("#tasarım_toplam").html(data.tasarım_toplam);
  $("#yda_marka_toplam").html(data.yda_marka_toplam);
  $("#yim_marka_toplam").html(data.yim_marka_toplam);
  /**
   * Patent
   */
  $("#patent_sadece_taksit_sayisi").html(data.patent_sadece_taksit_sayisi);
  $("#patent_belge_aldi_sayisi").html(data.patent_belge_aldi_sayisi);
  $("#patent_basvuru_asamasinda_sayisi").html(
    data.patent_basvuru_asamasinda_sayisi
  );
  $("#patent_musteri_takibinde_sayisi").html(
    data.patent_musteri_takibinde_sayisi
  );
  $("#patent_terk_sayisi").html(data.patent_terk_sayisi);
  $("#patent_dosya_no_degisikligi_sayisi").html(
    data.patent_dosya_no_degisikligi_sayisi
  );
  $("#patent_mukerrer_kayit_sayisi").html(data.patent_mukerrer_kayit_sayisi);
  $("#patent_vekil_degisikligi_sayisi").html(
    data.patent_vekil_degisikligi_sayisi
  );
  $("#patent_basvuru_oncesi_sayisi").html(data.patent_basvuru_oncesi_sayisi);
  $("#patent_koruma_suresi_dolmustur_sayisi").html(
    data.patent_koruma_suresi_dolmustur_sayisi
  );
  $("#patent_ulke_asamasi_sayisi").html(data.patent_ulke_asamasi_sayisi);
  $("#patent_ret_sayisi").html(data.patent_ret_sayisi);

  showCard(
    [
      {
        className: "patent_sadece_taksit_sayisi",
        dataName: "patent_sadece_taksit_sayisi",
        name: "Sadece Taksit",
      },
      {
        className: "patent_belge_aldi_sayisi",
        dataName: "patent_belge_aldi_sayisi",
        name: "Belge Aldı",
      },
      {
        className: "patent_basvuru_asamasinda_sayisi",
        dataName: "patent_basvuru_asamasinda_sayisi",
        name: "Başvuru Aşamasında",
      },
      {
        className: "patent_musteri_takibinde_sayisi",
        dataName: "patent_musteri_takibinde_sayisi",
        name: "Müşteri Takibinde",
      },
      {
        className: "patent_terk_sayisi",
        dataName: "patent_terk_sayisi",
        name: "Terk",
      },
      {
        className: "patent_dosya_no_degisikligi_sayisi",
        dataName: "patent_dosya_no_degisikligi_sayisi",
        name: "Dosya No Değişikliği",
      },
      {
        className: "patent_mukerrer_kayit_sayisi",
        dataName: "patent_mukerrer_kayit_sayisi",
        name: "Mükerrer Kayıt",
      },
      {
        className: "patent_vekil_degisikligi_sayisi",
        dataName: "patent_vekil_degisikligi_sayisi",
        name: "Vekil Değişikliği",
      },
      {
        className: "patent_basvuru_oncesi_sayisi",
        dataName: "patent_basvuru_oncesi_sayisi",
        name: "Başvuru Öncesinde",
      },
      {
        className: "patent_koruma_suresi_dolmustur_sayisi",
        dataName: "patent_koruma_suresi_dolmustur_sayisi",
        name: "Koruma Süresi Dolmuştur",
      },
      {
        className: "patent_ulke_asamasi_sayisi",
        dataName: "patent_ulke_asamasi_sayisi",
        name: "Ülke Aşaması",
      },
      {
        className: "patent_ret_sayisi",
        dataName: "patent_ret_sayisi",
        name: "Ret",
      },
    ],
    data,
    "patent"
  );

  /**
   * Yurtiçi Marka
   */
  $("#basvuru_sayisi").html(data.basvuru_sayisi);
  $("#yayin_sayisi").html(data.yayin_sayisi);
  $("#yenileme_terk_sayisi").html(data.yenileme_terk_sayisi);
  $("#tescil_sayisi").html(data.tescil_sayisi);
  $("#karara_itiraz_sayisi").html(data.karara_itiraz_sayisi);
  $("#red_sayisi").html(data.red_sayisi);
  $("#kismi_red_sayisi").html(data.kismi_red_sayisi);
  $("#yayina_itiraz_sayisi").html(data.yayina_itiraz_sayisi);
  $("#feragat_sayisi").html(data.feragat_sayisi);
  $("#tescil_belgesi_talep_edildi_sayisi").html(
    data.tescil_belgesi_talep_edildi_sayisi
  );
  $("#dava_kismi_sayisi").html(data.dava_kismi_sayisi);
  $("#tescil_karari_sayisi").html(data.tescil_karari_sayisi);
  $("#musteri_takibinde_sayisi").html(data.musteri_takibinde_sayisi);
  $("#dava_sonucu_hukumsuz_sayisi").html(data.dava_sonucu_hukumsuz_sayisi);
  $("#muddet_sayisi").html(data.muddet_sayisi);

  showCard(
    [
      {
        className: "basvuru_sayisi",
        dataName: "basvuru_sayisi",
        name: "Başvuru",
      },
      {
        className: "yayin_sayisi",
        dataName: "yayin_sayisi",
        name: "Yayın",
      },
      {
        className: "yenileme_terk_sayisi",
        dataName: "yenileme_terk_sayisi",
        name: "Yenileme Terk",
      },
      {
        className: "tescil_sayisi",
        dataName: "tescil_sayisi",
        name: "Tescilli",
      },
      {
        className: "karara_itiraz_sayisi",
        dataName: "karara_itiraz_sayisi",
        name: "Karara İtiraz",
      },
      {
        className: "red_sayisi",
        dataName: "red_sayisi",
        name: "Red",
      },
      {
        className: "kismi_red_sayisi",
        dataName: "kismi_red_sayisi",
        name: "Kısmi Red",
      },
      {
        className: "yayina_itiraz_sayisi",
        dataName: "yayina_itiraz_sayisi",
        name: "Yayına İtiraz",
      },
      {
        className: "feragat_sayisi",
        dataName: "feragat_sayisi",
        name: "Feragat Edildi/Geri Çekildi",
      },
      {
        className: "tescil_belgesi_talep_edildi_sayisi",
        dataName: "tescil_belgesi_talep_edildi_sayisi",
        name: "Tescil Belgesi Talep Edildi",
      },
      {
        className: "dava_kismi_sayisi",
        dataName: "dava_kismi_sayisi",
        name: "Dava Kısmi",
      },
      {
        className: "tescil_karari_sayisi",
        dataName: "tescil_karari_sayisi",
        name: "Tescil Kararı",
      },
      {
        className: "musteri_takibinde_sayisi",
        dataName: "musteri_takibinde_sayisi",
        name: "Müşteri Takibinde",
      },
      {
        className: "dava_sonucu_hukumsuz_sayisi",
        dataName: "dava_sonucu_hukumsuz_sayisi",
        name: "Dava sonucu hükümsüz",
      },
      {
        className: "muddet_sayisi",
        dataName: "muddet_sayisi",
        name: "Müddet",
      },
    ],
    data,
    "yurtici"
  );

  /**
   * Yurtdışı Marka
   */

  $("#yda_basvuru_sayisi_cart").html(data.yda_basvuru_sayisi);
  $("#yda_yayin_sayisi").html(data.yda_yayin_sayisi);
  $("#yda_tescil_sayisi").html(data.yda_tescil_sayisi);
  $("#yda_arastirma_yapildi_sayisi").html(data.yda_arastirma_yapildi_sayisi);
  $("#yda_kismi_tescil_sayisi").html(data.yda_kismi_tescil_sayisi);
  $("#yda_basvuru_yapilmadi_sayisi").html(data.yda_basvuru_yapilmadi_sayisi);
  $("#yda_talimat_bekleniyor_sayisi").html(data.yda_talimat_bekleniyor_sayisi);
  $("#yda_yenilenmedi_sayisi").html(data.yda_yenilenmedi_sayisi);
  $("#yda_red_sayisi").html(data.yda_red_sayisi);
  $("#yda_red_kararina_cevap_verildi_sayisi").html(
    data.yda_red_kararina_cevap_verildi_sayisi
  );
  $("#yda_basvuru_icin_bekleniyor_sayisi").html(
    data.yda_basvuru_icin_bekleniyor_sayisi
  );
  $("#yda_tescil_karari_alindi_sayisi").html(
    data.yda_tescil_karari_alindi_sayisi
  );
  $("#yda_hukumsuz_sayisi").html(data.yda_hukumsuz_sayisi);
  $("#yda_kismi_red_sayisi").html(data.yda_kismi_red_sayisi);
  $("#yda_terk_sayisi").html(data.yda_terk_sayisi);
  $("#yda_musteri_takibinde_sayisi").html(data.yda_musteri_takibinde_sayisi);
  $("#yda_itiraza_cevap_verildi_sayisi").html(
    data.yda_itiraza_cevap_verildi_sayisi
  );
  $("#yda_kabul_sayisi").html(data.yda_kabul_sayisi);
  $("#yda_basvuru_talimati_iletildi_sayisi").html(
    data.yda_basvuru_talimati_iletildi_sayisi
  );
  $("#yda_geri_cekildi_sayisi").html(data.yda_geri_cekildi_sayisi);
  $("#yda_kismi_kabul_sayisi").html(data.yda_kismi_kabul_sayisi);
  $("#yda_iptal_sayisi").html(data.yda_iptal_sayisi);
  $("#yda_arastirma_sayisi").html(data.yda_arastirma_sayisi);
  $("#yda_ulusal_basvuruya_donusturulmustur_sayisi").html(
    data.yda_ulusal_basvuruya_donusturulmustur_sayisi
  );
  $("#yda_itiraz_sayisi").html(data.yda_itiraz_sayisi);
  $("#yda_dava_sayisi").html(data.yda_dava_sayisi);
  $("#yda_islemde_sayisi").html(data.yda_islemde_sayisi);
  $("#yda_yayinlanacak_sayisi").html(data.yda_yayinlanacak_sayisi);

  showCard(
    [
      {
        className: "yda_basvuru_sayisi_cart",
        dataName: "yda_basvuru_sayisi",
        name: "Başvuru",
      },
      {
        className: "yda_yayin_sayisi",
        dataName: "yda_yayin_sayisi",
        name: "Yayın",
      },
      {
        className: "yda_tescil_sayisi",
        dataName: "yda_tescil_sayisi",
        name: "Tescil",
      },
      {
        className: "yda_terk_sayisi",
        dataName: "yda_terk_sayisi",
        name: "Terk",
      },
      {
        className: "yda_arastirma_yapildi_sayisi",
        dataName: "yda_arastirma_yapildi_sayisi",
        name: "Araştırma yapıldı",
      },
      {
        className: "yda_kismi_tescil_sayisi",
        dataName: "yda_kismi_tescil_sayisi",
        name: "Kısmi Tescil",
      },
      {
        className: "yda_basvuru_yapilmadi_sayisi",
        dataName: "yda_basvuru_yapilmadi_sayisi",
        name: "Başvuru Yapılmadı",
      },
      {
        className: "yda_yenilenmedi_sayisi",
        dataName: "yda_yenilenmedi_sayisi",
        name: "Yenilenmedi",
      },
      {
        className: "yda_red_sayisi",
        dataName: "yda_red_sayisi",
        name: "Red",
      },
      {
        className: "yda_red_kararina_cevap_verildi_sayisi",
        dataName: "yda_red_kararina_cevap_verildi_sayisi",
        name: "Red kararına cevap verildi",
      },

      {
        className: "yda_basvuru_icin_bekleniyor_sayisi",
        dataName: "yda_basvuru_icin_bekleniyor_sayisi",
        name: "Başvuru için bekleniyor",
      },
      {
        className: "yda_tescil_karari_alindi_sayisi",
        dataName: "yda_tescil_karari_alindi_sayisi",
        name: "Tescil Kararı alındı",
      },
      {
        className: "yda_hukumsuz_sayisi",
        dataName: "yda_hukumsuz_sayisi",
        name: "Hükümsüz",
      },
      {
        className: "yda_kismi_red_sayisi",
        dataName: "yda_kismi_red_sayisi",
        name: "Kısmi Red",
      },
      {
        className: "yda_musteri_takibinde_sayisi",
        dataName: "yda_musteri_takibinde_sayisi",
        name: "Müşteri Takibinde",
      },
      {
        className: "yda_itiraza_cevap_verildi_sayisi",
        dataName: "yda_itiraza_cevap_verildi_sayisi",
        name: "İtiraza cevap verildi",
      },
      {
        className: "yda_kabul_sayisi",
        dataName: "yda_kabul_sayisi",
        name: "Kabul",
      },
      {
        className: "yda_basvuru_talimati_iletildi_sayisi",
        dataName: "yda_basvuru_talimati_iletildi_sayisi",
        name: "Başvuru talimatı iletildi",
      },
      {
        className: "yda_geri_cekildi_sayisi",
        dataName: "yda_geri_cekildi_sayisi",
        name: "Geri Çekildi",
      },
      {
        className: "yda_kismi_kabul_sayisi",
        dataName: "yda_kismi_kabul_sayisi",
        name: "Kısmi Kabul",
      },
      {
        className: "yda_iptal_sayisi",
        dataName: "yda_iptal_sayisi",
        name: "İptal",
      },
      {
        className: "yda_arastirma_sayisi",
        dataName: "yda_arastirma_sayisi",
        name: "Araştırma",
      },
      {
        className: "yda_ulusal_basvuruya_donusturulmustur_sayisi",
        dataName: "yda_ulusal_basvuruya_donusturulmustur_sayisi",
        name: "Ulusal başvuruya dönüştürülmüştür",
      },
      {
        className: "yda_itiraz_sayisi",
        dataName: "yda_itiraz_sayisi",
        name: "İtiraz",
      },
      {
        className: "yda_dava_sayisi",
        dataName: "yda_dava_sayisi",
        name: "Dava",
      },
      {
        className: "yda_islemde_sayisi",
        dataName: "yda_islemde_sayisi",
        name: "İşlemde",
      },
      {
        className: "yda_yayinlanacak_sayisi",
        dataName: "yda_yayinlanacak_sayisi",
        name: "Yayınlanacak",
      },
    ],
    data,
    "yurtdisi"
  );
  /**
    Tasarım 
    */
  $("#tasarim_musteri_takibinde_sayisi").html(
    data.tasarim_musteri_takibinde_sayisi
  );
  $("#tasarim_yayin_sayisi").html(data.tasarim_yayin_sayisi);
  $("#tasarim_terk_sayisi").html(data.tasarim_terk_sayisi);
  $("#tasarim_feragat_sayisi").html(data.tasarim_feragat_sayisi);
  $("#tasarim_red_sayisi").html(data.tasarim_red_sayisi);
  $("#tasarim_dosya_no_degisikligi_sayisi").html(
    data.tasarim_dosya_no_degisikligi_sayisi
  );
  $("#tasarim_mukerrer_kayit_sayisi").html(data.tasarim_mukerrer_kayit_sayisi);
  $("#tasarim_tescil_sayisi").html(data.tasarim_tescil_sayisi);
  $("#patent_basvuru_sayisi").html(data.patent_basvuru_sayisi);

  $("#tasarim_basvuru_oncesinde_sayisi").html(
    data.tasarim_basvuru_oncesinde_sayisi
  );

  $("#tasarim_tescil_belgesi_talep_edildi_sayisi").html(
    data.tasarim_tescil_belgesi_talep_edildi_sayisi
  );
  $("#tasarim_tescil_karari_sayisi").html(data.tasarim_tescil_karari_sayisi);
  $("#tasarim_basvuru_asamasinda_sayisi").html(
    data.tasarim_basvuru_asamasinda_sayisi
  );
  $("#tasarim_koruma_suresi_dolmustur_sayisi").html(
    data.tasarim_koruma_suresi_dolmustur_sayisi
  );
  $("#tasarim_yenileme_terk_sayisi").html(data.tasarim_yenileme_terk_sayisi);
  $("#tasarim_yenileme_sayisi").html(data.tasarim_yenileme_sayisi);
  $("#tasarim_kismi_tescil_sayisi").html(data.tasarim_kismi_tescil_sayisi);
  $("#tasarim_karara_itiraz_sayisi").html(data.tasarim_karara_itiraz_sayisi);
  $("#tasarim_tescile_itiraz_sayisi").html(data.tasarim_tescile_itiraz_sayisi);
  $("#tasarim_kismi_red_sayisi").html(data.tasarim_kismi_red_sayisi);
  $("#tasarim_vekil_degisikligi_sayisi").html(
    data.tasarim_vekil_degisikligi_sayisi
  );

  $("#tasarim_basvuru_sayisi").html(data.tasarim_basvuru_sayisi);

  showCard(
    [
      {
        className: "tasarim_yayin_sayisi",
        dataName: "tasarim_yayin_sayisi",
        name: "Yayın",
      },
      {
        className: "tasarim_basvuru_sayisi",
        dataName: "tasarim_basvuru_sayisi",
        name: "Başvuru",
      },
      {
        className: "tasarim_tescil_sayisi",
        dataName: "tasarim_tescil_sayisi",
        name: "Tescil",
      },
      {
        className: "patent_musteri_takibinde_sayisi",
        dataName: "patent_musteri_takibinde_sayisi",
        name: "Müşteri Takibinde",
      },
      {
        className: "tasarim_terk_sayisi",
        dataName: "tasarim_terk_sayisi",
        name: "Terk",
      },
      {
        className: "tasarim_basvuru_oncesinde_sayisi",
        dataName: "tasarim_basvuru_oncesinde_sayisi",
        name: "Başvuru Öncesinde",
      },
      {
        className: "tasarim_feragat_sayisi",
        dataName: "tasarim_feragat_sayisi",
        name: "Feraget/Edildi/Geri Çekildi",
      },
      {
        className: "tasarim_red_sayisi",
        dataName: "tasarim_red_sayisi",
        name: "Red",
      },
      {
        className: "tasarim_dosya_no_degisikligi_sayisi",
        dataName: "tasarim_dosya_no_degisikligi_sayisi",
        name: "Dosya No Değişikliği",
      },
      {
        className: "tasarim_mukerrer_kayit_sayisi",
        dataName: "tasarim_mukerrer_kayit_sayisi",
        name: "Mükerrer Kayıt",
      },
      {
        className: "tasarim_tescil_belgesi_talep_edildi_sayisi",
        dataName: "tasarim_tescil_belgesi_talep_edildi_sayisi",
        name: "Tescil Belgesi Talep Edildi",
      },
      {
        className: "tasarim_tescil_karari_sayisi",
        dataName: "tasarim_tescil_karari_sayisi",
        name: "Tescil Kararı",
      },
      {
        className: "tasarim_koruma_suresi_dolmustur_sayisi",
        dataName: "tasarim_koruma_suresi_dolmustur_sayisi",
        name: "Koruma Süresi Dolmuştur",
      },
      {
        className: "tasarim_yenileme_terk_sayisi",
        dataName: "tasarim_yenileme_terk_sayisi",
        name: "Yenileme Terk",
      },
      {
        className: "tasarim_yenileme_sayisi",
        dataName: "tasarim_yenileme_sayisi",
        name: "Yenileme",
      },
      {
        className: "tasarim_kismi_tescil_sayisi",
        dataName: "tasarim_kismi_tescil_sayisi",
        name: "Kısmi Tescil",
      },
      {
        className: "tasarim_karara_itiraz_sayisi",
        dataName: "tasarim_karara_itiraz_sayisi",
        name: "Karara İtiraz",
      },
      {
        className: "tasarim_tescile_itiraz_sayisi",
        dataName: "tasarim_tescile_itiraz_sayisi",
        name: "Tescile İtiraz",
      },
      {
        className: "tasarim_kismi_red_sayisi",
        dataName: "tasarim_kismi_red_sayisi",
        name: "Kısmi Red",
      },
      {
        className: "tasarim_vekil_degisikligi_sayisi",
        dataName: "tasarim_vekil_degisikligi_sayisi",
        name: "Vekil Değişikliği",
      },
    ],
    data,
    "tasarim"
  );
}


function ileriBtn() {
  // trigger
  $('.owl-prev').trigger('click');
}

function geriBtn() {
  // trigger
  $('.owl-next').trigger('click');
}