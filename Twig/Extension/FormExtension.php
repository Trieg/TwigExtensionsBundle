<?php

namespace Craue\TwigExtensionsBundle\Twig\Extension;

use Craue\TwigExtensionsBundle\Util\TwigFeatureDefinition;
use Craue\TwigExtensionsBundle\Util\TwigFeatureUtil;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\Form\FormView;

/**
 * Twig extension for form handling.
 *
 * @author Christian Raue <christian.raue@gmail.com>
 * @copyright 2011-2016 Christian Raue
 * @license http://opensource.org/licenses/mit-license.php MIT License
 */
class FormExtension extends \Twig_Extension {

	/**
	 * @var FormFactoryInterface
	 */
	protected $formFactory;

	/**
	 * @var string
	 */
	protected $cloneFormAlias = null;

	public function setFormFactory(FormFactoryInterface $formFactory) {
		$this->formFactory = $formFactory;
	}

	/**
	 * @param string $cloneFormAlias Alias for the cloneForm function.
	 */
	public function setAliases($cloneFormAlias = null) {
		if (!empty($cloneFormAlias)) {
			$this->cloneFormAlias = $cloneFormAlias;
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function getName() {
		return 'craue_form';
	}

	/**
	 * {@inheritDoc}
	 */
	public function getFunctions() {
		return TwigFeatureUtil::getTwigFunctions($this, array(
			new TwigFeatureDefinition('craue_cloneForm', 'cloneForm', $this->cloneFormAlias),
		));
	}

	/**
	 * @param mixed $value A FormInterface or a FormTypeInterface.
	 * @param array $formOptions Options to pass to the form type (only valid if $value is a FormTypeInterface, ignored otherwise).
	 * @return FormView
	 * @throws \InvalidArgumentException
	 */
	public function cloneForm($value, array $formOptions = array()) {
		if ($value instanceof FormInterface) {
			return $value->createView();
		}

		if ($value instanceof FormTypeInterface) {
			if ($this->formFactory === null) {
				throw new \RuntimeException('No form factory available.');
			}

			$valueToUse = method_exists('Symfony\Component\Form\AbstractType', 'getBlockPrefix') ? get_class($value) : $value;

			return $this->formFactory->create($valueToUse, null, $formOptions)->createView();
		}

		throw new \InvalidArgumentException(sprintf('Expected argument of either type "%s" or "%s", but "%s" given.',
				'Symfony\Component\Form\FormTypeInterface',
				'Symfony\Component\Form\FormInterface',
				is_object($value) ? get_class($value) : gettype($value)
		));
	}

}
