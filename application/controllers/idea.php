<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

class Idea extends MY_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('idea');
    }

    public function add() {
        if(!$this->user->current) show_error('You need to be logged in to access this action');

        $subject     = trim($this->input->post('subject'));
        $description = trim($this->input->post('description'));

        if($this->input->post('save')) {
            if(empty($subject) || empty($description)) {
                $this->msg('Please fill in a subject and a detailed description', -1);
            } else {
                $id = $this->idea->add($subject, $description, $this->user->current->login);
                if($id !== false) {
                    $this->msg('Your idea was added.', 1);
                    redirect('idea/show/'.$id);
                } else {
                    $this->msg('Something went wrong, please try again', -1);
                }
            }
        }

        $this->load->view(
            'idea-form',
            array(
                 'subject'     => $subject,
                 'description' => $description
            )
        );
    }

    public function edit($ideaID) {
        if(!$this->user->current) show_error('You need to be logged in to access this action');
        if(!$this->user->current->role > 0) show_error('You need to be moderator to access this action');

        $subject     = trim($this->input->post('subject'));
        $description = trim($this->input->post('description'));

        if($this->input->post('save')) {
            if(empty($subject) || empty($description)) {
                $this->msg('Please fill in a subject and a detailed description', -1);
            } else {
                $ok = $this->idea->edit($ideaID, $subject, $description);
                if($ok !== false) {
                    $this->msg('The idea was edited.', 1);
                    redirect('idea/show/'.$ideaID);
                } else {
                    $this->msg('Something went wrong, please try again', -1);
                }
            }
        }

        $idea = $this->idea->get($ideaID);
        if(!$idea) show_404();

        $subject     = $idea->title;
        $description = $idea->description;

        $this->load->view(
            'idea-form',
            array(
                 'subject'     => $subject,
                 'description' => $description,
                 'ideaID'      => $ideaID
            )
        );
    }

    public function show($ideaID) {
        $idea = $this->idea->get($ideaID);
        if(!$idea) show_404();

        if($this->user->current && $this->user->current->role > 0) {
            if($this->input->post('statechange')) {
                $this->idea->setStatus($ideaID, (int) $this->input->post('state'));
                $this->msg('State changed.', 1);
                redirect('idea/show/'.$ideaID);
            }
        }

        $this->load->view('idea-show', array('idea' => $idea));
    }
}