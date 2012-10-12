<?php
namespace Arnm\MediaBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;
/**
 * Template form use to manage Templates as well as gets embedded into page form
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MediaType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('name', 'text', array(
        'label' => 'media.form.name.label',
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'media.form.name.help',
            'class' => 'span4'
        ), 
        'translation_domain' => 'media',
        'required' => false
    ));
    $builder->add('tag', 'text', array(
        'label' => 'media.form.tag.label',
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'media.form.tag.help',
            'class' => 'span4'
        ), 
        'translation_domain' => 'media',
        'required' => false
    ));
    $builder->add('media', 'file', array(
        'label' => 'media.form.media.label',
        'attr' => array(
            'rel' => 'tooltip', 
            'title' => 'media.form.media.help',
            'class' => 'input-file'
        ), 
        'translation_domain' => 'media',
        'required' => false
    ));
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.FormTypeInterface::getName()
   */
  public function getName()
  {
    return 'media';
  }
  
  /**
   * (non-PHPdoc)
   * @see Symfony\Component\Form.AbstractType::getDefaultOptions()
   */
  public function getDefaultOptions(array $options)
  {
    return array(
        'data_class' => 'Arnm\MediaBundle\Entity\Media'
    );
  }
}
