<?php
namespace App;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ConvertirCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('convertir')
            ->setDescription('Convertit un fichier vers json, xml ou csv')
            ->addArgument('fichier', InputArgument::REQUIRED, 'Nom du fichier')
            ->addArgument('format', InputArgument::REQUIRED, 'json, xml ou csv')
            ->addArgument('sortie', InputArgument::OPTIONAL, 'Fichier de sortie');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fichier = $input->getArgument('fichier');
        $format = $input->getArgument('format');

        if (!file_exists($fichier)) {
            $output->writeln("<error>Fichier introuvable</error>");
            return Command::FAILURE;
        }

        $source = pathinfo($fichier, PATHINFO_EXTENSION);

        // 1. Lire : transformer le fichier en tableau PHP
        if ($source === 'json') {
            $lignes = json_decode(file_get_contents($fichier), true);
            if (!is_array($lignes)) {
                $output->writeln("<error>JSON invalide</error>");
                return Command::FAILURE;
            }
        } elseif ($source === 'csv') {
            $tableau = array_map('str_getcsv', file($fichier, FILE_IGNORE_NEW_LINES));
            $entete = array_shift($tableau);
            $lignes = array_map(fn($l) => array_combine($entete, $l), $tableau);
        } else {
            $output->writeln("<error>Conversion impossible depuis $source</error>");
            return Command::FAILURE;
        }

        // 2. Écrire : fabriquer le texte dans $resultat
        if ($format === 'json' && $source !== 'json') {
            $resultat = json_encode($lignes, JSON_PRETTY_PRINT);
        } elseif ($format === 'xml') {
            $xml = new \SimpleXMLElement('<lignes/>');
            foreach ($lignes as $ligne) {
                $noeud = $xml->addChild('ligne');
                foreach ($ligne as $clef => $valeur) {
                    $noeud->addChild($clef, htmlspecialchars($valeur));
                }
            }
            $resultat = $xml->asXML();
        } elseif ($format === 'csv' && $source !== 'csv') {
            $resultat = implode(",", array_keys($lignes[0])) . "\n";
            foreach ($lignes as $ligne) {
                $resultat .= implode(",", $ligne) . "\n";
            }
        } else {
            $output->writeln("<error>Conversion $source vers $format impossible</error>");
            return Command::FAILURE;
        }

        // 3. Afficher ou enregistrer
        $sortie = $input->getArgument('sortie');
        if ($sortie) {
            file_put_contents($sortie, $resultat);
            $output->writeln("Fichier créé : $sortie");
        } else {
            $output->writeln($resultat);
        }

        return Command::SUCCESS;
    }
}