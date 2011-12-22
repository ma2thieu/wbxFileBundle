<?php

namespace wbx\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class FileType extends AbstractType {

    public function buildForm(FormBuilder $builder, array $options) {
        $builder
            ->add('name', 'text', array('required' => false))
            ->add('file', 'file', array('required' => true))
            ->add('is_file_changed', 'hidden');
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
