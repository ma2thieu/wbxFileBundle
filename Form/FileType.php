<?php

namespace wbx\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileType extends AbstractType {
	
	protected $with_empty;
    
	public function __construct($with_empty = false) {
	    $this->with_empty = $with_empty;
	}

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
            ->add('name', 'text', array('required' => false))
            ->add('file')
            ->add('is_file_changed', 'hidden');

		if ($this->with_empty) {
		    $builder->add('to_empty', 'checkbox', array(
		        'label' => 'Empty',
		        'required' => false,
		    ));
		}
    }

    public function getName() {
        return 'wbx_filebundle_filetype';
    }

	public function getDefaultOptions(array $options) {
        return array(
            'data_class' => 'wbx\FileBundle\Entity\File',
        );
    }

}
