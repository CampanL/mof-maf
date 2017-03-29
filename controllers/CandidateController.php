<?php 

namespace Controllers;

use Symfony\Component\HttpFoundation\Request;
use Silex\Application as App;
use Symfony\Component\Validator\Constraints as Assert;
use Models\Contracts as Contracts;
use Models\Etablishments as Etablishments;

class CandidateController extends Controllers{

	public function __construct( App $app ){

		parent::__construct($app,'candidates');

		$this->access = ['admin','organizer'];
	}

	public function create(){
		$this->utils->accessVerif();

		return $this->app['twig']->render('fronts/register.twig');
	}

	public function save(Request $request){
		$this->utils->accessVerif();

		//Recupère les donnée session
		$data = $this->app['session']->get('registration');
		
		//id candidat
		
		$candidatId = $this->model->selectWhere('ID>0 ORDER BY id DESC LIMIT 1');

		$candidatId = $candidatId[0]->id+1;

		//Models contracts
		$contracts = new Contracts($this->app);
		//Ajout du nouveau contrat
		$contracts->create($data['contract']);
		//Recupere le dernier id de la base
		$contractId = $contracts->selectWhere('ID>0 ORDER BY id DESC LIMIT 1');

		$contractId = $contractId[0]->id;

		//Model Etablishment
		$etablishments = new Etablishments($this->app);
		//Ajout du nouvelle etablissement
		$etablishments->create($data['etablishment']);
		//Recupere le dernier id de la base
		$etablishmentsId = $etablishments->selectWhere('ID>0 ORDER BY id DESC LIMIT 1');

		$etablishmentsId = $etablishmentsId[0]->id++;

		$candidate = $data['candidate'];
		//Chemin de sauvegarde des images
		$folder = 'assets/candidats/'.$candidatId.'_'.strtoupper($candidate['name'].'_'.$candidate['second_name']);
		//Suppression de l'extgension
		$folder = str_replace('.png', '', $folder);
		$folder = str_replace('.jpeg', '', $folder);
		$folder = str_replace('.jpg', '', $folder);
		$folder = str_replace('.pdf', '', $folder);

		//chemin des fichier
		$idCardFileName = $folder.'/'.'carte_identite.pdf';
		$schoolCertifFileName = $folder.'/'.'certificat_de_scolarite.pdf';

		//Création du dossier
		if (!file_exists($folder)) mkdir($folder, 0777, true);

		//Copie les fichier depuis le fichier tmp
		copy('assets/tmp/'.$candidate['identity_card']['name'],$idCardFileName);
		unlink('assets/tmp/'.$candidate['identity_card']['name']);

		copy('assets/tmp/'.$candidate['school_certificate']['name'],$schoolCertifFileName);
		unlink('assets/tmp/'.$candidate['school_certificate']['name']);

		//Normalisation des données
		$candidate['school_certificate'] = $schoolCertifFileName;
		$candidate['identity_card'] = $idCardFileName;
		$candidate['id_contract'] = $contractId;
		$candidate['id_etablishment'] = $etablishmentsId;
		$candidate['status'] = 'waiting';

		$response = $this->model->create($candidate);

		$this->app['session']->set('registration',null);

		return $this->app->redirect('/');
	}

	public function delete(Request $request){
		$this->utils->accessVerif($this->access);

		$id = $request->attributes->all()['id'];

		$candidate = $this->model->read($id);

		//Models contracts
		$contracts = new Contracts($this->app);
		//Suppression contrat
		$contracts->delete($candidate->id_contract);

		//Model Etablishment
		$etablishments = new Etablishments($this->app);
		//Suppression  etablissement
		$etablishments->delete($candidate->id_etablishment);

		//Suppression candidat
		
		$this->model->delete($id);


		$this->app['session']->getFlashBag()->add('alert', 'Utilisateur supprimé');
		return $this->app->redirect('/'.'admin/'.$this->table);
	}

	public function toSession(Request $request){
		$data = $request->request->all();

		$data['identity_card'] = $_FILES['identity_card'];
		$data['school_certificate'] = $_FILES['school_certificate'];

		move_uploaded_file($_FILES['identity_card']['tmp_name'],'assets/tmp/'.$_FILES['identity_card']['name']);
		move_uploaded_file($_FILES['school_certificate']['tmp_name'],'assets/tmp/'.$_FILES['school_certificate']['name']);

		move_uploaded_file($data['identity_card']['tmp_name'],'assets'.$data['identity_card']['name']);
		$this->app['session']->set('registration',['candidate'=>$data,'step'=>2]);

		return $this->app->redirect('/candidate/create');
	}

	public function edit($id){
		$this->utils->accessVerif($this->access);

		$candidate = $this->model->read($id);
		
		$etablishment = new Etablishments($this->app);
		
		$etablishment = $etablishment->selectWhere('ID = '.$candidate->id_etablishment);

		$contract = new Contracts($this->app);
		
		$contract = $contract->selectWhere('ID = '.$candidate->id_contract);

		return $this->app['twig']->render('fronts/backOffice/'.$this->table.'/edit.twig',['candidate'=>$candidate,'contract'=>$contract[0],'etablishment'=>$etablishment[0]]);
	}

	public function update(Request $request){
		$this->utils->accessVerif($this->access);

		$request = $request->request->all();

		$candidateData = [];
		$contractData = [];
		$etablishmentData = [];

		foreach ($request as $key => $value) {
			if ( strstr($key, 'contract')) {
				$key = explode("-", $key)[1];
				$contractData[$key] = $value;
			}elseif (strstr($key, 'etablishment')) {
				$key = explode("-", $key)[1];
				$etablishmentData[$key] = $value;
			}else{
				$candidateData[$key] = $value;
			}
		}

		$contractModel = new Contracts($this->app);

		$contractModel->update($contractData);

		$etablishmentModel = new Etablishments($this->app);

		$etablishmentModel->update($etablishmentData);

		$this->model->update($candidateData);

		return $this->app->redirect('/'.'admin/candidates');
	}
}