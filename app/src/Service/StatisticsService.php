<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\JsonResponse;

class StatisticsService
{

    private $account_ref = null;
    public function __construct($registry, $request)
    {
        $this->db = $registry->getConnection();
        $this->account_ref = $request->getSession()->get('ref_account');
    }


    public function generalStatistics()
    {

        $sql = "SELECT
        (SELECT COUNT(*) FROM v_tm_yim_file where col_is_deleted = false AND ref_account = '$this->account_ref' ) AS yim_marka_toplam,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_is_deleted = false  and ref_account = '$this->account_ref') AS yda_marka_toplam,
        (SELECT COUNT(*) FROM v_patent_file WHERE silinmis = false and firma_id = '$this->account_ref') AS patent_toplam,
        (SELECT COUNT(*) FROM v_design_file WHERE silinmis = false and firma_id = '$this->account_ref') AS tasarım_toplam,
        /**
        * Patent Durum
         */
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Sadece Taksit' AND silinmis =false and firma_id = '$this->account_ref') AS patent_sadece_taksit_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Belge Aldı'  AND silinmis =false   and firma_id = '$this->account_ref') AS patent_belge_aldi_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Başvuru Aşamasında' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_basvuru_asamasinda_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Müşteri Takibinde' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_musteri_takibinde_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Terk' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_terk_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Dosya No Değişikliği' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_dosya_no_degisikligi_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Mükerrer Kayıt' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_mukerrer_kayit_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Vekil Değişikliği' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_vekil_degisikligi_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Başvuru Öncesi' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_basvuru_oncesi_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Koruma Süresi Dolmuştur' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_koruma_suresi_dolmustur_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Ülke Aşaması' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_ulke_asamasi_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Ret' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_ret_sayisi,
        (SELECT COUNT(*) FROM v_patent_file WHERE son_durum_detay = 'Başvuru' AND silinmis =false  and firma_id = '$this->account_ref') AS patent_basvuru_sayisi,
        /**
        * Yurtiçi Marka Durum
         */
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Başvuru' AND col_is_deleted =false  and ref_account = '$this->account_ref' ) AS basvuru_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Yayın' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yayin_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Yenileme Terk' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yenileme_terk_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Tescilli' AND col_is_deleted =false and ref_account = '$this->account_ref') AS tescil_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Tescil belgesi talep edildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS tescil_belgesi_talep_edildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Red' AND col_is_deleted =false and ref_account = '$this->account_ref') AS red_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Yayına İtiraz' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yayina_itiraz_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Dava(Kısmi)' AND col_is_deleted =false and ref_account = '$this->account_ref') AS dava_kismi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Feragat Edildi/Geri Çekildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS feragat_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Kısmi Red' AND col_is_deleted =false and ref_account = '$this->account_ref') AS kismi_red_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Tescil Kararı' AND col_is_deleted =false and ref_account = '$this->account_ref') AS tescil_karari_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Terk' AND col_is_deleted =false and ref_account = '$this->account_ref') AS terk_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Karara İtiraz' AND col_is_deleted =false and ref_account = '$this->account_ref') AS karara_itiraz_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Müşteri Takibinde' AND col_is_deleted =false and ref_account = '$this->account_ref') AS musteri_takibinde_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Dava sonucu hükümsüz' AND col_is_deleted =false and ref_account = '$this->account_ref') AS dava_sonucu_hukumsuz_sayisi,
        (SELECT COUNT(*) FROM v_tm_yim_file WHERE col_last_status = 'Müddet' AND col_is_deleted =false and ref_account = '$this->account_ref') AS muddet_sayisi,
        /**
        * Yurtdışı Marka Durum 
         */
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Başvuru' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_basvuru_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Yayın' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_yayin_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Tescil' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_tescil_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Terk' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_terk_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Araştırma yapıldı' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_arastirma_yapildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Kısmi Tescil' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_kismi_tescil_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Başvuru Yapılmadı' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_basvuru_yapilmadi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Talimat bekleniyor' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_talimat_bekleniyor_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Yenilenmedi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_yenilenmedi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Red' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_red_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Red kararına cevap verildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_red_kararina_cevap_verildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Başvuru için bekleniyor' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_basvuru_icin_bekleniyor_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Tescil Kararı alındı' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_tescil_karari_alindi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Hükümsüz' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_hukumsuz_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Kısmi Red' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_kismi_red_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Müşteri Takibinde' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_musteri_takibinde_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'İtiraza cevap verildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_itiraza_cevap_verildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Kabul' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_kabul_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Başvuru talimatı iletildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_basvuru_talimati_iletildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Geri Çekildi' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_geri_cekildi_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Kısmi kabul' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_kismi_kabul_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'İptal' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_iptal_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Araştırma' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_arastirma_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Ulusal başvuruya dönüştürülmüştür' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_ulusal_basvuruya_donusturulmustur_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'İtiraz' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_itiraz_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Dava' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_dava_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'İşlemde' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_islemde_sayisi,
        (SELECT COUNT(*) FROM v_tm_yda_file WHERE col_last_status = 'Yayınlanacak' AND col_is_deleted =false and ref_account = '$this->account_ref') AS yda_yayinlanacak_sayisi,

        /**
         * Tasarım Durum
         */
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Müşteri Takibinde' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_musteri_takibinde_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Yayın' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_yayin_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Terk' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_terk_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Feragat/Edildi/Geri Çekildi' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_feragat_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Başvuru Öncesinde' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_basvuru_oncesinde_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Red' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_red_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Dosya No Değişikliği' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_dosya_no_degisikligi_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Mükerrer Kayıt' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_mukerrer_kayit_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Tescil' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_tescil_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Tescil Belgesi Talep Edildi' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_tescil_belgesi_talep_edildi_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Tescil Kararı' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_tescil_karari_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Başvuru' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_basvuru_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Başvuru Aşamasında' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_basvuru_asamasinda_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Koruma Süresi Dolmuştur' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_koruma_suresi_dolmustur_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Yenileme Terk' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_yenileme_terk_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Yenileme' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_yenileme_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Kısmi Tescil' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_kismi_tescil_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Karara İtiraz' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_karara_itiraz_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Tescile İtiraz' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_tescile_itiraz_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Kısmi Red' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_kismi_red_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Vekil Değişikliği' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_vekil_degisikligi_sayisi,
        (SELECT COUNT(*) FROM v_design_file WHERE son_durum_detay = 'Yenileme' AND silinmis =false and firma_id = '$this->account_ref') AS tasarim_yenileme_sayisi
        ";
        $stmt = $this->db->prepare($sql);
        $stmt = $stmt->execute();
        $result = $stmt->fetchAllAssociative();

        if ($result) {
            return new JsonResponse([
                'status' => true,
                'data' => $result,
                'message' => 'İşlem Başarılı'
            ]);
        } else {
            return new JsonResponse([
                'status' => false,
                'data' => null,
                'message' => 'İşlem Başarısız'
            ]);
        }
    }
}
