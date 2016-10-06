<?php
	use Symfony\Component\Console\Input\InputInterface;
	use Symfony\Component\Console\Output\OutputInterface;
	use Symfony\Component\Console\Input\ArrayInput;
	use Symfony\Component\Process\Process;
	use Symfony\Component\Process\Exception\ProcessFailedException;
	use Symfony\Component\Console\Input\InputArgument;
	use Symfony\Component\Console\Input\InputOption;
	use Symfony\Component\Console\Command\Command;
	use Symfony\Component\Console\Formatter\OutputFormatterStyle;
	use Symfony\Component\Process\ProcessBuilder;

	/**
	 * Created by PhpStorm.
	 * User: clops
	 * Date: 12/02/2016
	 * Time: 12:27
	 */
	class SiegeCommand extends Command{

		/**
		 * Setup command signature
		 */
		protected function configure() {
			$this->setName("siege")
				 ->setDescription("Stress Test the Application")
				 ->addOption('generate',    'g', InputOption::VALUE_OPTIONAL, 'Full Update including all dependancies',     100)
				 ->addOption('concurrent',  'c', InputOption::VALUE_OPTIONAL, 'Number of concurrent threads to run',        10)
				 ->addOption('repetitions', 'r', InputOption::VALUE_OPTIONAL, 'Number of repetitions per thread per URL',   10)
			;
		}


		/**
		 * @param InputInterface $input
		 * @param OutputInterface $output
		 */
		protected function execute(InputInterface $input, OutputInterface $output){
			$this->setupOutputStyling( $output );

			//first start generation of test data
			$this->generateTestData($input, $output);

			//start the actual stress test
			$this->siegeTest($input, $output);

			//now remove all test data
			$this->removeTestData($input, $output);

			//and email test results to the admin if needed
			$this->emailTestResults($input, $output);

			//and finally some garbage collection
			$this->garbageCollector($input, $output);

			//fin
			$output->writeln('Done! Thank you :)');
		}


		/**
		 * Prepare some default styles for the console output
		 * @param OutputInterface $output
		 */
		private function setupOutputStyling(OutputInterface $output){
			$header_style = new OutputFormatterStyle('white', 'green', array('bold'));
			$output->getFormatter()->setStyle('header', $header_style);
		}


		/**
		 * Generates test data for Siege
		 *
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 *
		 * @throws Exception
		 */
		private function generateTestData(InputInterface $input, OutputInterface $output){
			$cardinality = (int)$input->getOption('generate');

			if($cardinality <= 0){
				throw new Exception('Cardinality must be an integer larger than 0');
			}
			$output->writeln('<header>Starting Generation of Test Data</header>');
			$output->writeln('Cardinality: '.$cardinality);
			$output->write('Generating '.$cardinality.' Users... ');
			$output->writeln('done');
			$output->write('Generating '.$cardinality.' Producers... ');
			$output->writeln('done');
			$output->write('Generating '.$cardinality.' Products per Producer... ');
			$output->writeln('done');
		}


		/**
		 * Removes all generated test data
		 *
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 */
		private function removeTestData(InputInterface $input, OutputInterface $output){
			$output->writeln('<header>Removing Test Data</header>');
			$output->write('Removing Products per Producer... ');
			$output->writeln('done');
			$output->write('Removing Producers... ');
			$output->writeln('done');
			$output->write('Removing Users... ');
			$output->writeln('done');
		}


		/**
		 * This will start Siege with all the provided options
		 *
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 */
		private function siegeTest(InputInterface $input, OutputInterface $output){
			$output->writeln('');
			$output->writeln('<header>Initiating Siege</header>');

			$concurrent  = (int)$input->getOption('concurrent');
			$repetitions = (int)$input->getOption('repetitions');

			if($concurrent <= 0){
				throw new Exception('Concurrent must be an integer larger than 0, '.$concurrent.' given');
			}

			if($repetitions <= 0){
				throw new Exception('Repetitions must be an integer larger than 0, '.$repetitions.' given');
			}

			$builder = new ProcessBuilder(
				array(
					'siege',
					'--rc=config/.siegerc',
					'--file=config/urls.txt',
					'--log=var/log/siege.log',
					'--concurrent='.$concurrent,
					'--reps='.$repetitions
				)
			);

			$siege = $builder->getProcess();
			$siege->setTimeout( 0 ); //unlimited

			$siege->mustRun(
				function ($type, $buffer) use ( $output ) {
					$output->writeln( $buffer );
				}
			);

		}


		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 */
		private function emailTestResults(InputInterface $input, OutputInterface $output){
			$output->writeln('');
			$output->writeln('<header>EMailing Test Results to Maintainer</header>');

			//emailing results with an attachment
			//@todo
		}


		/**
		 * @param InputInterface  $input
		 * @param OutputInterface $output
		 */
		private function garbageCollector(InputInterface $input, OutputInterface $output){
			//collect garbage here, remove old log files, and anything else
			//@todo
		}

	}