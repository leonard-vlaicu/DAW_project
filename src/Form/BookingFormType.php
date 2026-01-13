<?php

namespace App\Form;

use App\Entity\Booking;
use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BookingFormType extends AbstractType {
    public function __construct() {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void {
        $builder
            ->add('beginDate', TextType::class, [
                'required' => false,
            ])
            ->add('endDate', TextType::class, [
                'required' => false,
            ]);

        $builder->get('beginDate')->addModelTransformer(new CallbackTransformer(
            function ($dateTime) {
                return $dateTime ? $dateTime->format('Y-m-d') : '';
            },
            function ($dateString) {
                return $dateString ? new DateTime($dateString) : null;
            }
        ));

        $builder->get('endDate')->addModelTransformer(new CallbackTransformer(
            function ($dateTime) {
                return $dateTime ? $dateTime->format('Y-m-d') : '';
            },
            function ($dateString) {
                return $dateString ? new DateTime($dateString) : null;
            }
        ));

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $booking = $event->getData();

            if (!$booking instanceof Booking) {
                return;
            }

            $this->validateLogicalDate($booking, $form);
            $this->validateBusinessDate($booking, $form);
        });
    }

    private function validateLogicalDate(Booking $booking, FormInterface $form): void {
        if ($booking->getBeginDate() && $booking->getEndDate()) {
            $beginDate = $booking->getBeginDate();
            $endDate = $booking->getEndDate();
            $today = new DateTime('today');


            $interval = $beginDate->diff($endDate);
            $daysDifference = (int)$interval->format('%r%a');

            if ($beginDate < $today) {
                $form->get('beginDate')->addError(new FormError('The begin date cannot be in the past.'));
            }
            if ($endDate < $today) {
                $form->get('endDate')->addError(new FormError('The end date cannot be in the past.'));
            }

            if ($daysDifference < 1) {
                $form->get('endDate')->addError(new FormError('The end date must be after the begin date.'));
            } elseif ($daysDifference > 30) {
                $form->get('endDate')->addError(new FormError('The maximum number of days is 30.'));
            }
        }
    }

    private function validateBusinessDate(Booking $booking, FormInterface $form): void {
        if ($booking->getBeginDate() && $booking->getEndDate()) {
            $beginDate = $booking->getBeginDate();
            $endDate = $booking->getEndDate();
            $availableBooks = [];

            $book = $booking->getBook();
            foreach ($book->getBookings() as $alreadyBookedBooking) {
                if ($beginDate < $alreadyBookedBooking->getEndDate() && $endDate > $alreadyBookedBooking->getBeginDate()) {
                    $availableBooks[] = $alreadyBookedBooking;
                }
            }

            if ($book->getCopies() - count($availableBooks) == 0) {
                $form->get('beginDate')->addError(new FormError('The selected dates are not available.'));
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
