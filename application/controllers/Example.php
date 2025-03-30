<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Example extends MY_Controller {

	/**
	 * Index Page for this controller.
	 *
	 * Maps to the following URL
	 * 		http://example.com/index.php/welcome
	 *	- or -
	 * 		http://example.com/index.php/welcome/index
	 *	- or -
	 * Since this controller is set as the default controller in
	 * config/routes.php, it's displayed at http://example.com/
	 *
	 * So any other public methods not prefixed with an underscore will
	 * map to /index.php/welcome/<method_name>
	 * @see https://codeigniter.com/user_guide/general/urls.html
	 */
	public function list_get() {
		echo '{"code":20000,"data":{"items":[{"id":"530000198502263389","title":"Wumu ylhdcso buli mokf muj lfdm biqrhuln wyyqqllpah xkjc jqevhixye odmsfxzzh.","status":"published","author":"name","display_time":"2006-10-13 14:38:19","pageviews":2823},{"id":"64000019930515284X","title":"Ygoqtdh bzhskhvm xxhseqdgqb eqieesunt oarmcm mdxnxy usuddi tjeqvgqls ovqlrefwg gimqebqql vwsvp tdtke yojlvifvnn kes.","status":"published","author":"name","display_time":"1981-08-10 03:41:08","pageviews":2851},{"id":"710000197908147125","title":"Amnygpt dqqnglcmk emeijtin sjowh snxfpgt ctocw bptm pouypi oivkll znwmvfwljs yufhw ofdknkqhe opykjolgb mov rnav mmcr.","status":"published","author":"name","display_time":"2000-04-22 12:09:55","pageviews":2453},{"id":"120000199604181737","title":"Vwor xhope scfmsik qkelbvklpr lwvfc wwum sofjmmkwny wdbkxmp lnvlnoljcv etgpdr jhbbkx yixuifg pvjcmc lqcvqscz fokkl psi qgqbdyxb bnqlmj usselqbc.","status":"draft","author":"name","display_time":"1976-05-30 14:26:52","pageviews":3683},{"id":"210000200212164724","title":"Vxdlniub qdwoeyw dhvujqti geyd ksrrtpe mxloydn jpruiyp dsmglbiof ftqjrndwwo dwhtmphes uvtlsk qptpvrg spdnfsveh vmiean lktmdweltw.","status":"draft","author":"name","display_time":"2012-09-10 09:25:29","pageviews":1490},{"id":"610000199506235531","title":"Xozf nxfdktw lfdccw zcxdhvcv bquruhc dndstdg rkbcgsw fvdao hfwhu lirzi.","status":"published","author":"name","display_time":"1974-03-02 00:50:57","pageviews":3102},{"id":"500000197102179700","title":"Osirg ctfcohdf yurxrgk lyb ufluglx bglwlbsdoo xre verrf oyteo ssef hgpwv ajdhlm ulahdy eybl ksbi cjhennql.","status":"draft","author":"name","display_time":"1973-05-13 10:15:43","pageviews":4675},{"id":"140000201608100707","title":"Qfwpalwceu jbhlfrqvm eugioylur saqwcednx onmpjctn brxccjv kayw uzrlufbdk eyiqbifux qcdsdpwtt qwivq pjyqxb uezgfce mtjes rbon gdakspp.","status":"deleted","author":"name","display_time":"1972-03-30 00:24:30","pageviews":2900},{"id":"320000201309016736","title":"Weuykmcya xubnyoqnm tobl dumsbud lgwsaawqi inlumxx pqqityjqdj lxvjijsp uefy lydo xygjoxl gybpnd ofw mspxxn hjexlrytg gxylnsmc excrrmebq mrnrptlqo mbuw.","status":"deleted","author":"name","display_time":"1970-06-07 12:16:32","pageviews":3376},{"id":"330000201709033211","title":"Nasuho pwitkato vgdrndq zrtpvaw ryluop ccsd bkbf behgvgwqc erbanelj wwapyivrd peoksobizu nabdkc adxg pnkwf sxlqwyfxh fghxzrp mxnkwjmte rxocptt.","status":"draft","author":"name","display_time":"2006-05-16 23:06:40","pageviews":2613},{"id":"360000200806308169","title":"Mqfcs ufniwf wmx qhwv tjbvlxt poyhkbmlx tdtclxxufz ntnv xnuravae xoezwztprn kyjqj ycub sjpxplytue sfb bztoy.","status":"deleted","author":"name","display_time":"2011-05-14 01:30:45","pageviews":2823},{"id":"460000197007117808","title":"Trdg vvo oqnbchmrsb dtsjnvn rxmuu ivufakjnx wcwylcon gcofqef nuvqo pumbhu jjbzojyt wmtthgl ukbitou gselu coqxv.","status":"deleted","author":"name","display_time":"1993-08-25 03:46:34","pageviews":3201},{"id":"500000197103314599","title":"Mksq tcdf ntpj trqwi imwjyrgl ehdtduca dirapyneyq pbfgbl ikrotpc yeptuuvz jhxjplfhve uthptnfufh.","status":"draft","author":"name","display_time":"1998-02-20 09:56:19","pageviews":2573},{"id":"220000200812277468","title":"Nopogjtm bgqp kmtq frvfxbknni hwcygun gpddkprr xdvifu qtlxypx bymusqvw oqdlvctjyp.","status":"draft","author":"name","display_time":"2010-12-10 05:07:06","pageviews":2830},{"id":"350000201305226117","title":"Hynhknkws ymgvtlnva odddpbfxf dwuyhnt yliec zmqnhizcp jnuyfxpv glxsl fwutewlqcg lgvq.","status":"draft","author":"name","display_time":"2002-01-18 21:25:45","pageviews":682},{"id":"230000197511058840","title":"Nrhcyvr isxiiix fncpvj hrcvyzqs owph qvaexfji yuwgqdisf vtspg uysfl goqwjpmf uycj bqhlhjbi.","status":"deleted","author":"name","display_time":"1989-04-27 12:35:18","pageviews":781},{"id":"710000200208042650","title":"Oxyqfx ljg rllsrgcn xngosndax hdhjgaep hktjy psscpjqan pygu ogtc chdcg buecs ckbbocgxq cdgaujzrzv uuacyvqrtc ogbsmbs.","status":"draft","author":"name","display_time":"2007-03-15 05:06:13","pageviews":977},{"id":"360000199203152921","title":"Fczd tiagqgbo glblin sky euwl tnrt cyig vkk zygf dlrufq nwf wzjygtj wdfnxplrsw qhgeqmo.","status":"draft","author":"name","display_time":"1987-03-30 09:47:27","pageviews":3551},{"id":"360000201503121924","title":"Clgrfks byucng akebt uqnmdqdl euwwk ofo ddx qupxmhmsom gbywrx eoiavlreyy bhvocctuo msgr fdbjgkhpw.","status":"draft","author":"name","display_time":"1988-01-03 11:29:09","pageviews":4294},{"id":"110000197812062432","title":"Pxvditeow vpql pijoen imp gqujvgp lpil hjbfw oekpttxt hgfvgnw tigoskkskw ygrkff bptlaxr mkccgw axtqd knbfmncm sbw cmo jhirsc mswlxmr pwc.","status":"deleted","author":"name","display_time":"1975-11-10 03:42:22","pageviews":3500},{"id":"620000199012024728","title":"Ivhxwkab sci yiowx rjryvy glbfnh wlmfnso wjgi yhbhtczkip qhkqs hygygyw lyyqvvrjm yuhgiv efkqfxzos vgwrmjfjop kfcnfgxam uodbm vkboayntbt.","status":"draft","author":"name","display_time":"1983-05-06 16:49:08","pageviews":1258},{"id":"650000197609114407","title":"Xtgl xgjfykpqu xuislbg vrakxcu vosfxlh iqr fmitbc uwymmtif qeigevgkgy xoumri ucdqcpm hkypkc srebfu mohhkn zyzbbp.","status":"draft","author":"name","display_time":"2016-09-20 21:19:14","pageviews":4499},{"id":"420000201109196078","title":"Blcay npgenz dyoyadenm aiouutnb xruni ovjclnbbr hxfrbry tunwgmejk nopadne mqemh ulionvvj aiyd mhhngnr elg mscpsbjko imenk mxxjwfxx.","status":"draft","author":"name","display_time":"1978-01-10 00:14:00","pageviews":605},{"id":"440000198910096661","title":"Cnxkv qeohcpr efmglo cfpdkvex ckvhxx tosodk siuk jdxddnobyq yijmznegzd uxx hxcabemft xnwwkusil.","status":"draft","author":"name","display_time":"1999-02-19 14:56:53","pageviews":1453},{"id":"15000019831105193X","title":"Iwseyrql lvhwo dnunty dgt jxtm uliy pfvlq xhkqyqec ypqupkjnje nvns hihayfd snxylqqtdv dilgiuablz uoskcxgyp trwoner emem fqnbbtgyr.","status":"published","author":"name","display_time":"2007-09-27 20:33:26","pageviews":4655},{"id":"37000019771118157X","title":"Qvrgy vlthkr iubom rwgpvlilw dpriowbey ftami lpv vlkzojcuc gibrie trotvpacci mnv lgfibaz orwpcchc rqjlxmf aqubjvnn behkywsn dtwfhmb.","status":"published","author":"name","display_time":"1970-09-29 02:39:05","pageviews":1780},{"id":"410000197108106260","title":"Moww fpevzs pfqed fgcdj wkilu bxdm lbeew mweimruj yhgrtkkj mwmbcplia pbuahwgie mfodfeku xthwmqu iyp djksyrw dfdebhumgf kedixc umfr.","status":"published","author":"name","display_time":"2016-05-02 20:43:56","pageviews":828},{"id":"630000201012256766","title":"Jvw bjmkan nqzbqb qwtlbsngv eos nebxfjfd fzqbcfbqzy xetff qlfqhsb ivn ghoikhq tdxmg uliek.","status":"published","author":"name","display_time":"2012-11-28 17:36:37","pageviews":1267},{"id":"510000200201267921","title":"Mpyshw lnjgrsvd hayvfinl yodmucplh mvxevu scdgbvygi cxliqapt yxl ghqgpddokx buekvoqu fedhfqek gwjjyrc hclfdsif ufxjl vswa kvdupexiv.","status":"deleted","author":"name","display_time":"1981-12-10 14:58:42","pageviews":3365},{"id":"640000200605044758","title":"Drmgnci ylybccjh kexir wcaxil nvyxda mbjqoukw ahtkmecu ksybdoc utxel jszhkkrq kbftivrnwh.","status":"published","author":"name","display_time":"1985-02-13 22:00:17","pageviews":2962}]}}';
	}

	// Ejemplo mandar notificaciones Android/iOs sacado de la aplicación de Juan Valera
  public function addCalificacion_post() {
    $post = $this->post();
    $id = $this->calificacion->insert($post);

		// [...]

    if($id != null) {
      $post['id'] = $id;
      $post['nombre_asignatura'] = $this->asignatura->get_by('id', $post['asignatura_id'])->nombre;

			// Buscamos los ids de los usuarios a los que queramos mandar notificación
      $datos_alumno_asignatura = $this->alumno_asignatura->get_many_by('asignatura_id', $post['asignatura_id']);
      $ids_alumnos = array_column($datos_alumno_asignatura, 'alumno_id');
			// Llamamos al método para preparar las notificaciones y le pasamos el objeto de lo que queramos notificar y los ids de usuarios
      $this->enviarNotificacionCalificacion($post, $ids_alumnos);

      $this->response($this->lang->line('success_crear_calificacion'), self::HTTP_OK, self::CODE_SHOW_SUCCESS_MESSAGE);
    } else {
      $this->response($this->lang->line('error_crear_calificacion'), self::HTTP_OK, self::CODE_BAD);
    }
  }

  private function enviarNotificacionCalificacion($calificacion, $ids_alumnos=array()) {
    $texto_a_mandar = 'Asignatura: ' . $calificacion['nombre_asignatura'];
		// Creamos el objeto que vamos a mandar para la notificación
    $datos_a_mandar = array(
      'TEXTO' => $texto_a_mandar,
			// El campo "EXTRA" no es obligatorio, en este caso, es para que desde la
			// aplicación android se pudiera redirigir a una pantalla específica
      'EXTRA' => array(
        'TIPO' => 'CALIFICACIÓN',
        'ID_OBJETO' => $calificacion['id']
      )
    );

		// Ponemos un título para la notificación
    $datos_a_mandar['TITULO'] = 'Nueva Calificación';
		// Llamamos a un método para sacar los tokens de los usuarios recibidos para
		// android y otro para iOS (porque las notificaciones se mandan de distinta forma)
    $keysFromDatabaseForAndroid = $this->notificacion_token->getTokensForThisUserIdsOnAndroid($ids_alumnos);
    $keysFromDatabaseForIos = $this->notificacion_token->getTokensForThisUserIdsOnIos($ids_alumnos);

		// Llamamos a los métodos para mandar las notificaciones tanto a Android como a iOS
    $this->notificacion_token->sendNotifications($keysFromDatabaseForAndroid, false, $datos_a_mandar);
    $this->notificacion_token->sendNotifications($keysFromDatabaseForIos, true, $datos_a_mandar);
  }

}
