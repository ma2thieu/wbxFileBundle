<?php

namespace wbx\FileBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FileType extends AbstractType {

    protected $with_empty;
    protected $with_name;

    public function __construct($with_empty = false, $with_name = false) {
        $this->with_empty = $with_empty;
        $this->with_name = $with_name;
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('file', 'file', array(
                'required'  => false,
                'label'     => 'wbxfilebundle.form.file'
            ))
            ->add('is_file_changed', 'hidden');

        if ($this->with_name) {
            $builder->add('name', 'text', array(
                'required'  => false,
                'label'     => 'wbxfilebundle.form.name'
            ));
        }

        if ($this->with_empty) {
            $builder->add('to_empty', 'checkbox', array(
                'required'  => false,
                'label'     => 'wbxfilebundle.form.empty'
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
