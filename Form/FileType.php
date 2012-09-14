<?php

namespace wbx\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType {

    protected $with_empty;

    public function __construct($with_empty = false) {
        $this->with_empty = $with_empty;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('name', 'text', array('required' => false))
            ->add('file', 'file')
            ->add('is_file_changed', 'hidden');

        if ($this->with_empty) {
            $builder->add('to_empty', 'checkbox', array(
                'label'     => 'Empty',
                'required'  => false,
            ));
        }
    }

    public function getName() {
        return 'wbx_filebundle_filetype';
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'wbx\FileBundle\Entity\File',
        ));
    }

}
