<?php

declare(strict_types=1);

namespace ThreeBRS\SyliusMailChimpPlugin\Form\Extension;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use ThreeBRS\SyliusMailChimpPlugin\Service\MailChimpManagerInterface;

final class MailChimpChannelTypeExtension extends AbstractTypeExtension
{
    public function __construct(private readonly MailChimpManagerInterface $mailChimpManager)
    {
    }

    /** @return array<string> */
    public static function getExtendedTypes(): array
    {
        return [
            ChannelType::class,
        ];
    }

    /** @param array<mixed> $options */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isMailChimpEnabled', CheckboxType::class, [
                'label' => 'threebrs.mailChimp.admin.form.channel.enabled.label',
            ])
            ->add('isMailChimpListDoubleOptInEnabled', CheckboxType::class, [
                'label' => 'threebrs.mailChimp.admin.form.channel.double_optin.label',
            ])
            ->add('mailChimpListId', ChoiceType::class, [
                'label' => 'threebrs.mailChimp.admin.form.channel.list.label',
                'placeholder' => 'threebrs.mailChimp.admin.form.channel.list.placeholder',
                'choice_loader' => new CallbackChoiceLoader(function () {
                    return array_flip($this->mailChimpManager->getLists());
                }),
            ]);
    }
}
